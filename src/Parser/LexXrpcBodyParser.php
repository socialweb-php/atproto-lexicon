<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;

use function is_object;
use function is_string;

/**
 * @phpstan-import-type LexXrpcBodyJson from LexXrpcBody
 */
class LexXrpcBodyParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexXrpcBody
    {
        /** @var LexXrpcBodyJson $data */
        $data = $this->validate($data, $this->getValidator());

        $schema = null;
        if (isset($data->schema)) {
            $schema = $this->getParserFactory()->getParser(LexObjectParser::class)->parse($data->schema);
        }

        return new LexXrpcBody(
            description: $data->description ?? null,
            encoding: $data->encoding,
            schema: $schema,
        );
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
