<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexBoolean;

use function is_bool;
use function is_string;

final class LexBooleanParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexBoolean
    {
        /** @var object{default?: bool, const?: bool, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'boolean'
                && (!isset($data->default) || is_bool($data->default))
                && (!isset($data->const) || is_bool($data->const))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexBoolean(
            default: $data->default ?? null,
            const: $data->const ?? null,
            description: $data->description ?? null,
        );
    }
}
