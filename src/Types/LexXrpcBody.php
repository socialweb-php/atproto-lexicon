<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-import-type TLexObject from LexObject
 * @phpstan-import-type TLexRefVariant from LexEntity
 * @phpstan-type TLexXrpcBody = object{
 *     description?: string,
 *     encoding: string,
 *     schema?: TLexObject | TLexRefVariant,
 * }
 */
class LexXrpcBody implements JsonSerializable, LexEntity
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?string $encoding = null,
        public readonly LexObject | LexRef | LexRefUnion | null $schema = null,
    ) {
        $this->schema?->setParent($this);
    }
}
