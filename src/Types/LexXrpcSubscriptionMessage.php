<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type TLexObject from LexObject
 * @phpstan-import-type TLexRefVariant from LexEntity
 * @phpstan-type TLexXrpcSubscriptionMessage = object{
 *     description?: string,
 *     schema?: TLexObject | TLexRefVariant,
 * }
 */
class LexXrpcSubscriptionMessage implements LexEntity
{
    public function __construct(
        public readonly ?string $description = null,
        public readonly LexObject | LexRef | LexRefUnion | null $schema = null,
    ) {
    }
}
