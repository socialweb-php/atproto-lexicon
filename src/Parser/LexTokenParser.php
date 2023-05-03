<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexToken;

use function is_string;

final class LexTokenParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexToken
    {
        /** @var object{description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'token'
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexToken(
            description: $data->description ?? null,
        );
    }
}
