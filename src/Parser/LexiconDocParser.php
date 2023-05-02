<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use ReflectionObject;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexImage;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexNumber;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexRecord;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexToken;
use SocialWeb\Atproto\Lexicon\Types\LexUnion;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;
use SocialWeb\Atproto\Lexicon\Types\LexUserType;
use SocialWeb\Atproto\Lexicon\Types\LexVideo;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

use function assert;
use function file_get_contents;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function ltrim;
use function sprintf;
use function str_starts_with;
use function strcasecmp;

final class LexiconDocParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    private const DEPTH = 5;

    /**
     * @var array<string, object>
     */
    private array $originalDefs = [];

    public function __construct(
        SchemaRepository $schemaRepository,
        ParserFactory $parserFactory,
        private readonly bool $resolveReferences = false,
        private readonly int $depth = 0,
    ) {
        $this->setSchemaRepository($schemaRepository);
        $this->setParserFactory($parserFactory);
    }

    public function parse(object | string $data): LexiconDoc
    {
        /** @var object{id: string, revision?: float | int, description?: string, defs?: object} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->lexicon)
                && Nsid::isValid($data->id ?? null)
                && (!isset($data->revision) || is_int($data->revision) || is_float($data->revision))
                && (!isset($data->description) || is_string($data->description))
                && (!isset($data->defs) || is_object($data->defs)),
        );

        $id = new Nsid($data->id);

        $existingDoc = $this->getSchemaRepository()->findSchemaByNsid($id);
        if ($existingDoc !== null) {
            return $existingDoc;
        }

        $revision = $data->revision ?? null;
        $description = $data->description ?? null;
        $originalDefs = $data->defs ?? (object) [];

        $validDefs = [];

        $reflectedDefs = new ReflectionObject($originalDefs);
        foreach ($reflectedDefs->getProperties() as $property) {
            $value = $property->getValue($originalDefs);
            assert(is_object($value));
            $validDefs[$property->getName()] = $value;
        }

        $this->originalDefs = $validDefs;

        $parsedDefs = [];
        foreach ($validDefs as $name => $def) {
            $parsedDefs[$name] = $this->parseDef($def);
        }

        $doc = new LexiconDoc(
            id: $id->nsid,
            defs: $parsedDefs,
            revision: $revision,
            description: $description,
        );

        $this->getSchemaRepository()->storeSchema($doc);

        return $doc;
    }

    private function parseDef(object $def): LexArray | LexPrimitive | LexRef | LexUnion | LexUserType
    {
        $type = $def->type ?? null;
        assert($type === null || is_string($type));

        return match ($type) {
            'array' => $this->parseArray($def),
            'audio' => $this->getParserFactory()->getParser(LexAudioParser::class)->parse($def),
            'blob' => $this->parseBlob($def),
            'boolean' => $this->getParserFactory()->getParser(LexBooleanParser::class)->parse($def),
            'image' => $this->parseImage($def),
            'integer' => $this->parseInteger($def),
            'number' => $this->parseNumber($def),
            'object' => $this->parseObject($def),
            'procedure' => $this->parseProcedure($def),
            'query' => $this->parseQuery($def),
            'record' => $this->parseRecord($def),
            'ref' => $this->parseRef($def),
            'string' => $this->parseString($def),
            'token' => $this->parseToken($def),
            'union' => $this->parseUnion($def),
            'unknown' => $this->parseUnknown($def),
            'video' => $this->parseVideo($def),
            default => throw new UnableToParse("Encountered unknown type \"$type\""),
        };
    }

    private function parseArray(object $def): LexArray
    {
        $items = $def->items ?? null;
        $minLength = $def->minLength ?? null;
        $maxLength = $def->maxLength ?? null;
        $description = $def->description ?? null;

        assert(is_object($items));
        assert($minLength === null || is_int($minLength));
        assert($maxLength === null || is_int($maxLength));
        assert($description === null || is_string($description));

        $items = $this->parseDef($items);
        assert(
            $items instanceof LexObject
            || $items instanceof LexPrimitive
            || $items instanceof LexRef
            || $items instanceof LexUnion,
            sprintf('Did not expect type of %s at line %d', $items::class, __LINE__),
        );

        return new LexArray($items, $minLength, $maxLength, $description);
    }

    private function parseBlob(object $def): LexBlob
    {
        $maxSize = $def->maxSize ?? null;
        $description = $def->description ?? null;

        /** @var string[] | null $accept */
        $accept = $def->accept ?? null;

        assert($accept !== null && $this->isArrayOfString($accept));
        assert($maxSize === null || is_int($maxSize) || is_float($maxSize));
        assert($description === null || is_string($description));

        return new LexBlob($accept, $maxSize, $description);
    }

    private function parseImage(object $def): LexImage
    {
        $description = $def->description ?? null;
        $maxSize = $def->maxSize ?? null;
        $maxWidth = $def->maxWidth ?? null;
        $maxHeight = $def->maxHeight ?? null;

        /** @var string[] | null $accept */
        $accept = $def->accept ?? null;

        assert($accept !== null && $this->isArrayOfString($accept));
        assert($maxSize === null || is_int($maxSize) || is_float($maxSize));
        assert($maxWidth === null || is_int($maxWidth) || is_float($maxWidth));
        assert($maxHeight === null || is_int($maxHeight) || is_float($maxHeight));
        assert($description === null || is_string($description));

        return new LexImage($accept, $maxSize, $maxWidth, $maxHeight, $description);
    }

    private function parseInteger(object $def): LexInteger
    {
        $default = $def->default ?? null;
        $minimum = $def->minimum ?? null;
        $maximum = $def->maximum ?? null;
        $const = $def->const ?? null;
        $description = $def->description ?? null;

        /** @var int[] | null $enum */
        $enum = $def->enum ?? null;

        assert($default === null || is_int($default));
        assert($minimum === null || is_int($minimum));
        assert($maximum === null || is_int($maximum));
        assert($enum === null || $this->isArrayOfInteger($enum));
        assert($const === null || is_int($const));
        assert($description === null || is_string($description));

        return new LexInteger($default, $minimum, $maximum, $enum, $const, $description);
    }

    /**
     * @param 'query' | 'procedure' $queryOrProcedure
     */
    private function parseMethod(object $def, string $queryOrProcedure): LexXrpcQuery | LexXrpcProcedure
    {
        $parameters = [];
        $errors = [];

        foreach ($def->parameters ?? [] as $name => $param) {
            assert(is_string($name));
            assert(is_object($param));
            $param = $this->parseDef($param);
            assert($param instanceof LexPrimitive);
            $parameters[$name] = $param;
        }

        foreach ($def->errors ?? [] as $error) {
            assert(is_object($error));
            $errors[] = $this->parseXrpcError($error);
        }

        $input = $def->input ?? null;
        $output = $def->output ?? null;
        $description = $def->description ?? null;

        assert($input === null || is_object($input));
        assert($output === null || is_object($output));
        assert($description === null || is_string($description));

        if ($input !== null) {
            $input = $this->parseXrpcBody($input);
        }

        if ($output !== null) {
            $output = $this->parseXrpcBody($output);
        }

        if ($queryOrProcedure === 'procedure') {
            return new LexXrpcProcedure($parameters ?: null, $input, $output, $errors ?: null, $description);
        }

        return new LexXrpcQuery($parameters ?: null, $output, $errors ?: null, $description);
    }

    private function parseNumber(object $def): LexNumber
    {
        $default = $def->default ?? null;
        $const = $def->const ?? null;
        $description = $def->description ?? null;
        $minimum = $def->minimum ?? null;
        $maximum = $def->maximum ?? null;

        /** @var list<int | float> | null $enum */
        $enum = $def->enum ?? null;

        assert($default === null || is_int($default) || is_float($default));
        assert($minimum === null || is_int($minimum) || is_float($minimum));
        assert($maximum === null || is_int($maximum) || is_float($maximum));
        assert($enum === null || $this->isArrayOfNumber($enum));
        assert($const === null || is_int($const) || is_float($const));
        assert($description === null || is_string($description));

        return new LexNumber($default, $minimum, $maximum, $enum, $const, $description);
    }

    private function parseObject(object $def): LexObject
    {
        $properties = [];

        /**
         * @var string $name
         * @var object $property
         */
        foreach ($def->properties ?? [] as $name => $property) {
            $property = $this->parseDef($property);
            assert(
                $property instanceof LexArray
                || $property instanceof LexBlob
                || $property instanceof LexObject
                || $property instanceof LexPrimitive
                || $property instanceof LexRef
                || $property instanceof LexUnion
                || $property instanceof LexUnknown,
                sprintf('Did not expect type of %s at line %d', $property::class, __LINE__),
            );
            $properties[$name] = $property;
        }

        $description = $def->description ?? null;

        /** @var string[] | null $required */
        $required = $def->required ?? null;

        assert($required === null || $this->isArrayOfString($required));
        assert($description === null || is_string($description));

        return new LexObject($properties, $required, $description);
    }

    private function parseProcedure(object $def): LexXrpcProcedure
    {
        /** @var LexXrpcProcedure */
        return $this->parseMethod($def, 'procedure');
    }

    private function parseQuery(object $def): LexXrpcQuery
    {
        /** @var LexXrpcQuery */
        return $this->parseMethod($def, 'query');
    }

    private function parseRecord(object $def): LexRecord
    {
        $record = $def->record ?? null;
        $key = $def->key ?? null;
        $description = $def->description ?? null;

        assert(is_object($record));
        assert($key === null || is_string($key));
        assert($description === null || is_string($description));

        return new LexRecord($this->parseObject($record), $key, $description);
    }

    private function parseRef(object $def): LexArray | LexPrimitive | LexRef | LexUnion | LexUserType
    {
        $ref = $def->ref ?? null;

        assert(is_string($ref));

        if ($this->resolveReferences) {
            if (str_starts_with($ref, '#')) {
                foreach ($this->originalDefs as $name => $refDef) {
                    if (strcasecmp($name, ltrim($ref, '#')) === 0) {
                        return $this->parseDef($refDef);
                    }
                }
            } else {
                $nsid = new Nsid($ref);
                $document = $this->getSchemaRepository()->findSchemaByNsid($nsid);

                if ($document === null && $this->depth < self::DEPTH) {
                    $contents = file_get_contents((string) $this->getSchemaRepository()->findSchemaPathByNsid($nsid));
                    $parser = new self(
                        $this->getSchemaRepository(),
                        $this->getParserFactory(),
                        $this->resolveReferences,
                        $this->depth + 1,
                    );
                    $document = $parser->parse((string) $contents);
                }

                if ($document !== null && isset($document->defs[$nsid->defId])) {
                    return $document->defs[$nsid->defId];
                }
            }
        }

        return new LexRef($ref);
    }

    private function parseString(object $def): LexString
    {
        $format = $def->format ?? null;
        $default = $def->default ?? null;
        $minLength = $def->minLength ?? null;
        $maxLength = $def->maxLength ?? null;
        $maxGraphemes = $def->maxGraphemes ?? null;
        $const = $def->const ?? null;
        $description = $def->description ?? null;

        /** @var string[] | null $enum */
        $enum = $def->enum ?? null;

        /** @var string[] | null $knownValues */
        $knownValues = $def->knownValues ?? null;

        assert($format === null || is_string($format));
        assert($default === null || is_string($default));
        assert($minLength === null || is_int($minLength));
        assert($maxLength === null || is_int($maxLength));
        assert($maxGraphemes === null || is_int($maxGraphemes));
        assert($enum === null || $this->isArrayOfString($enum));
        assert($const === null || is_string($const));
        assert($knownValues === null || $this->isArrayOfString($knownValues));
        assert($description === null || is_string($description));

        return new LexString(
            $format,
            $default,
            $minLength,
            $maxLength,
            $maxGraphemes,
            $enum,
            $const,
            $knownValues,
            $description,
        );
    }

    private function parseToken(object $def): LexToken
    {
        $description = $def->description ?? null;

        assert($description === null || is_string($description));

        return new LexToken($description);
    }

    private function parseUnion(object $def): LexUnion
    {
        /** @var string[] | null $refs */
        $refs = $def->refs ?? null;

        assert($refs !== null && $this->isArrayOfString($refs));

        if ($this->resolveReferences) {
            $resolvedRefs = [];
            foreach ($refs as $ref) {
                $resolvedRefs[] = $this->parseRef(new LexRef($ref));
            }
            $refs = $resolvedRefs;
        }

        return new LexUnion($refs);
    }

    private function parseUnknown(object $def): LexUnknown
    {
        $description = $def->description ?? null;

        assert($description === null || is_string($description));

        return new LexUnknown($description);
    }

    private function parseVideo(object $def): LexVideo
    {
        $maxSize = $def->maxSize ?? null;
        $maxLength = $def->maxLength ?? null;
        $maxWidth = $def->maxWidth ?? null;
        $maxHeight = $def->maxHeight ?? null;
        $description = $def->description ?? null;

        /** @var string[] | null $accept */
        $accept = $def->accept ?? null;

        assert($maxSize === null || is_int($maxSize) || is_float($maxSize));
        assert($maxLength === null || is_int($maxLength) || is_float($maxLength));
        assert($maxWidth === null || is_int($maxWidth) || is_float($maxWidth));
        assert($maxHeight === null || is_int($maxHeight) || is_float($maxHeight));
        assert($description === null || is_string($description));
        assert($accept !== null && $this->isArrayOfString($accept));

        return new LexVideo($accept, $maxSize, $maxWidth, $maxHeight, $maxLength, $description);
    }

    private function parseXrpcBody(object $def): LexXrpcBody
    {
        $encoding = $def->encoding ?? null;
        $schema = $def->schema ?? null;
        $description = $def->description ?? null;

        assert(is_string($encoding));
        assert(is_object($schema));
        assert($description === null || is_string($description));

        return new LexXrpcBody($encoding, $this->parseObject($schema), $description);
    }

    private function parseXrpcError(object $def): LexXrpcError
    {
        $name = $def->name ?? null;
        $description = $def->description ?? null;

        assert(is_string($name));
        assert($description === null || is_string($description));

        return new LexXrpcError($name, $description);
    }
}
