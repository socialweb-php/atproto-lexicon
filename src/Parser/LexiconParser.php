<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexRecord;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexUnion;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;

use function assert;
use function is_object;
use function is_string;
use function json_encode;
use function sprintf;

final class LexiconParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function __construct(
        ?SchemaRepository $schemaRepository = null,
        ?ParserFactory $parserFactory = null,
    ) {
        if ($schemaRepository !== null) {
            $this->setSchemaRepository($schemaRepository);
        }

        if ($parserFactory !== null) {
            $this->setParserFactory($parserFactory);
        }
    }

    public function parse(object | string $data): LexType
    {
        $data = $this->validate($data, fn (): bool => true);

        if (isset($data->lexicon) && isset($data->id)) {
            return $this->getParserFactory()->getParser(LexiconDocParser::class)->parse($data);
        }

        /** @var mixed $type */
        $type = $data->type ?? '';

        if (is_string($type)) {
            return match ($type) {
                'object' => $this->parseObject($data),
                'procedure' => $this->parseProcedure($data),
                'query' => $this->parseQuery($data),
                'record' => $this->parseRecord($data),
                'ref' => $this->parseRef($data),
                'union' => $this->parseUnion($data),
                default => $this->getParserFactory()->getParserByTypeName($type)->parse($data),
            };
        }

        throw new UnableToParse('Unknown object: ' . json_encode($data));
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
            $param = $this->parse($param);
            assert($param instanceof LexPrimitive);
            $parameters[$name] = $param;
        }

        foreach ($def->errors ?? [] as $error) {
            assert(is_object($error));
            $errors[] = (new LexXrpcErrorParser())->parse($error);
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

    private function parseObject(object $def): LexObject
    {
        $properties = [];

        /**
         * @var string $name
         * @var object $property
         */
        foreach ($def->properties ?? [] as $name => $property) {
            $property = $this->parse($property);
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

    private function parseRef(object $def): LexType
    {
        $ref = $def->ref ?? null;

        assert(is_string($ref));

        return new LexRef($ref);
    }

    private function parseUnion(object $def): LexUnion
    {
        /** @var string[] | null $refs */
        $refs = $def->refs ?? null;

        assert($refs !== null && $this->isArrayOfString($refs));

        return new LexUnion($refs);
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
}
