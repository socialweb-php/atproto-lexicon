<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexRefUnionJson from LexRefUnion
 * @phpstan-type LexRefJson = object{
 *     type: 'ref',
 *     description?: string,
 *     ref: string,
 * }
 * @phpstan-type LexRefVariantJson = LexRefJson | LexRefUnionJson
 */
final class LexRef implements LexType
{
    public readonly LexPrimitiveType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?string $ref = null,
    ) {
        $this->type = LexPrimitiveType::Ref;
    }
}
