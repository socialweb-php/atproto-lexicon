<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexPrimitiveJson from LexPrimitive
 * @phpstan-type LexPrimitiveArrayJson = object{
 *     type: 'array',
 *     description?: string,
 *     items?: LexPrimitiveJson,
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
