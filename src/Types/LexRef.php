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
class LexRef implements LexEntity
{
    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?string $ref = null,
    ) {
        $this->type = LexType::Ref;
    }
}
