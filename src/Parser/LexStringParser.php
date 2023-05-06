<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexStringFormat;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_int;
use function is_string;

/**
 * @phpstan-import-type TLexString from LexString
 */
class LexStringParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexString
    {
        /** @var TLexString $data */
        $data = $this->validate($data, $this->getValidator());

        /** @var string $format */
        $format = $data->format ?? '';

        return new LexString(
            format: LexStringFormat::tryFrom($format),
            description: $data->description ?? null,
            default: $data->default ?? null,
            minLength: $data->minLength ?? null,
            maxLength: $data->maxLength ?? null,
            minGraphemes: $data->minGraphemes ?? null,
            maxGraphemes: $data->maxGraphemes ?? null,
            enum: $data->enum ?? null,
            const: $data->const ?? null,
            knownValues: $data->knownValues ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::String->value
            && (!isset($data->format) || is_string($data->format))
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->default) || is_string($data->default))
            && (!isset($data->minLength) || is_int($data->minLength))
            && (!isset($data->maxLength) || is_int($data->maxLength))
            && (!isset($data->minGraphemes) || is_int($data->minGraphemes))
            && (!isset($data->maxGraphemes) || is_int($data->maxGraphemes))
            && (!isset($data->enum) || $this->isArrayOfString($data->enum))
            && (!isset($data->const) || is_string($data->const))
            && (!isset($data->knownValues) || $this->isArrayOfString($data->knownValues));
    }
}
