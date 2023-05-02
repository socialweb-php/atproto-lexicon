<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexInteger;

use function is_int;
use function is_string;

final class LexIntegerParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexInteger
    {
        /** @var object{default?: int, minimum?: int, maximum?: int, enum?: int[], const?: int, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'integer'
                && (!isset($data->default) || is_int($data->default))
                && (!isset($data->minimum) || is_int($data->minimum))
                && (!isset($data->maximum) || is_int($data->maximum))
                && (!isset($data->enum) || $this->isArrayOfInteger($data->enum))
                && (!isset($data->const) || is_int($data->const))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexInteger(
            default: $data->default ?? null,
            minimum: $data->minimum ?? null,
            maximum: $data->maximum ?? null,
            enum: $data->enum ?? null,
            const: $data->const ?? null,
            description: $data->description ?? null,
        );
    }
}
