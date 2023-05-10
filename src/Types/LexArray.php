<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-import-type TLexBlob from LexBlob
 * @phpstan-import-type TLexIpldType from LexEntity
 * @phpstan-import-type TLexPrimitive from LexPrimitive
 * @phpstan-import-type TLexRefVariant from LexEntity
 * @phpstan-type TLexArray = object{
 *     type: 'array',
 *     description?: string,
 *     items?: TLexBlob | TLexIpldType | TLexPrimitive | TLexRefVariant,
 *     minLength?: int,
 *     maxLength?: int,
 * }
 */
class LexArray implements JsonSerializable, LexUserType
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly LexBlob | LexBytes | LexCidLink | LexPrimitive | LexRef | LexRefUnion | null $items = null,
        public readonly ?int $minLength = null,
        public readonly ?int $maxLength = null,
    ) {
        $this->type = LexType::Array;
        $this->items?->setParent($this);
    }
}
