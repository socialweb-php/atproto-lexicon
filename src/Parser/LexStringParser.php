<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexString;

use function is_int;
use function is_string;

final class LexStringParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexString
    {
        /** @var object{format?: string, default?: string, minLength?: int, maxLength?: int, maxGraphemes?: int, enum?: string[], const?: string, knownValues?: string[], description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'string'
                && (!isset($data->format) || is_string($data->format))
                && (!isset($data->default) || is_string($data->default))
                && (!isset($data->minLength) || is_int($data->minLength))
                && (!isset($data->maxLength) || is_int($data->maxLength))
                && (!isset($data->maxGraphemes) || is_int($data->maxGraphemes))
                && (!isset($data->enum) || $this->isArrayOfString($data->enum))
                && (!isset($data->const) || is_string($data->const))
                && (!isset($data->knownValues) || $this->isArrayOfString($data->knownValues))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexString(
            format: $data->format ?? null,
            default: $data->default ?? null,
            minLength: $data->minLength ?? null,
            maxLength: $data->maxLength ?? null,
            maxGraphemes: $data->maxGraphemes ?? null,
            enum: $data->enum ?? null,
            const: $data->const ?? null,
            knownValues: $data->knownValues ?? null,
            description: $data->description ?? null,
        );
    }
}
