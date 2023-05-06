<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_int;
use function is_string;

/**
 * @phpstan-import-type TLexInteger from LexInteger
 */
class LexIntegerParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexInteger
    {
        /** @var TLexInteger $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexInteger(
            description: $data->description ?? null,
            default: $data->default ?? null,
            minimum: $data->minimum ?? null,
            maximum: $data->maximum ?? null,
            enum: $data->enum ?? null,
            const: $data->const ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Integer->value
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->default) || is_int($data->default))
            && (!isset($data->minimum) || is_int($data->minimum))
            && (!isset($data->maximum) || is_int($data->maximum))
            && (!isset($data->enum) || $this->isArrayOfInteger($data->enum))
            && (!isset($data->const) || is_int($data->const));
    }
}
