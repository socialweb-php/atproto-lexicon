<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexArrayJson = object{
 *     type: 'array',
 *     description?: string,
 *     items?: object,
 *     minLength?: int,
 *     maxLength?: int,
 * }
 */
final class LexArray implements LexType
{
    public readonly LexPrimitiveType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly LexObject | LexPrimitive | LexRef | LexRefUnion | LexUnknown | null $items = null,
        public readonly ?int $minLength = null,
        public readonly ?int $maxLength = null,
    ) {
        $this->type = LexPrimitiveType::Array;
    }
}
