<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;

use function is_string;

/**
 * @phpstan-import-type LexXrpcErrorJson from LexXrpcError
 */
final class LexXrpcErrorParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexXrpcError
    {
        /** @var LexXrpcErrorJson $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexXrpcError(
            name: $data->name,
            description: $data->description ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->name) && is_string($data->name)
            && (!isset($data->description) || is_string($data->description));
    }
}
