<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;

use function is_object;
use function is_string;

final class LexXrpcBodyParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexXrpcBody
    {
        /** @var object{encoding: string, schema: object, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->encoding) && is_string($data->encoding)
                && isset($data->schema) && is_object($data->schema)
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexXrpcBody(
            encoding: $data->encoding,
            schema: $this->getParserFactory()->getParser(LexObjectParser::class)->parse($data->schema),
            description: $data->description ?? null,
        );
    }
}
