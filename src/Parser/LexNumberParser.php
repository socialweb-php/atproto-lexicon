<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexNumber;

use function is_float;
use function is_int;
use function is_string;

class LexNumberParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexNumber
    {
        /** @var object{default?: float | int, minimum?: float | int, maximum?: float | int, enum?: list<float | int>, const?: float | int, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'number'
                && (!isset($data->default) || is_int($data->default) || is_float($data->default))
                && (!isset($data->minimum) || is_int($data->minimum) || is_float($data->minimum))
                && (!isset($data->maximum) || is_int($data->maximum) || is_float($data->maximum))
                && (!isset($data->enum) || $this->isArrayOfNumber($data->enum))
                && (!isset($data->const) || is_int($data->const) || is_float($data->const))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexNumber(
            default: $data->default ?? null,
            minimum: $data->minimum ?? null,
            maximum: $data->maximum ?? null,
            enum: $data->enum ?? null,
            const: $data->const ?? null,
            description: $data->description ?? null,
        );
    }
}
