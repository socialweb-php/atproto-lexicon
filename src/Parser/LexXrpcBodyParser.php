<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;

use function is_object;
use function is_string;

/**
 * @phpstan-import-type TLexObject from LexObject
 * @phpstan-import-type TLexRefVariant from LexEntity
 * @phpstan-import-type TLexXrpcBody from LexXrpcBody
 */
class LexXrpcBodyParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexXrpcBody
    {
        /** @var TLexXrpcBody $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexXrpcBody(
            description: $data->description ?? null,
            encoding: $data->encoding,
            schema: $this->parseSchema($data),
        );
    }

    /**
     * @phpstan-param TLexXrpcBody $data
     */
    private function parseSchema(object $data): LexObject | LexRef | LexRefUnion | null
    {
        /** @var TLexObject | TLexRefVariant | null $schema */
        $schema = $data->schema ?? null;
        $parsedSchema = null;

        if ($schema !== null) {
            $parsedSchema = $this->getParserFactory()->getParser(LexiconParser::class)->parse($schema);
        }

        if (
            $parsedSchema === null
            || $parsedSchema instanceof LexObject
            || $parsedSchema instanceof LexRef
            || $parsedSchema instanceof LexRefUnion
        ) {
            /** @var LexObject | LexRef | LexRefUnion | null */
            return $parsedSchema;
        }

        $this->throwParserError($data);
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => (!isset($data->description) || is_string($data->description))
            && isset($data->encoding) && is_string($data->encoding)
            && (!isset($data->schema) || is_object($data->schema));
    }
}
