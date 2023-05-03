<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;

use function is_string;

final class LexXrpcErrorParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexXrpcError
    {
        /** @var object{name: string, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->name)
                && is_string($data->name)
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexXrpcError(
            name: $data->name,
            description: $data->description ?? null,
        );
    }
}
