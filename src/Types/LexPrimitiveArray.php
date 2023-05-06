<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type TLexPrimitive from LexPrimitive
 * @phpstan-type TLexPrimitiveArray = object{
 *     type: 'array',
 *     description?: string,
 *     items?: TLexPrimitive,
 *     minLength?: int,
 *     maxLength?: int,
 * }
 */
class LexPrimitiveArray extends LexArray implements LexUserType
{
    public function __construct(
        ?string $description = null,
        LexPrimitive | null $items = null,
        ?int $minLength = null,
        ?int $maxLength = null,
    ) {
        parent::__construct($description, $items, $minLength, $maxLength);
    }
}
