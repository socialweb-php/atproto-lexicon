<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexBlobJson from LexBlob
 * @phpstan-import-type LexIpldTypeJson from LexCidLink
 * @phpstan-import-type LexPrimitiveJson from LexPrimitive
 * @phpstan-import-type LexRefVariantJson from LexRef
 * @phpstan-type LexArrayJson = object{
 *     type: 'array',
 *     description?: string,
 *     items?: LexBlobJson | LexIpldTypeJson | LexPrimitiveJson | LexRefVariantJson,
 *     minLength?: int,
 *     maxLength?: int,
 * }
 */
class LexArray implements LexUserType
{
    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly LexBlob | LexBytes | LexCidLink | LexPrimitive | LexRef | LexRefUnion | null $items = null,
        public readonly ?int $minLength = null,
        public readonly ?int $maxLength = null,
    ) {
        $this->type = LexType::Array;
    }
}
