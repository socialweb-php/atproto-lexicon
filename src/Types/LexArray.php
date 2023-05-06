<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexBlobJson from LexBlob
 * @phpstan-import-type LexPrimitiveJson from LexPrimitive
 * @phpstan-import-type LexRefVariantJson from LexRef
 * @phpstan-type LexArrayJson = object{
 *     type: 'array',
 *     description?: string,
 *     items?: LexBlobJson | LexPrimitiveJson | LexRefVariantJson,
 *     minLength?: int,
 *     maxLength?: int,
 * }
 */
final class LexArray implements LexUserType
{
    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly LexBlob | LexPrimitive | LexRef | LexRefUnion | null $items = null,
        public readonly ?int $minLength = null,
        public readonly ?int $maxLength = null,
    ) {
        $this->type = LexType::Array;
    }
}
