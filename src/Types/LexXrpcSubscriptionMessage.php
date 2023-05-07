<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-import-type TLexObject from LexObject
 * @phpstan-import-type TLexRefVariant from LexEntity
 * @phpstan-type TLexXrpcSubscriptionMessage = object{
 *     description?: string,
 *     schema?: TLexObject | TLexRefVariant,
 * }
 */
class LexXrpcSubscriptionMessage implements JsonSerializable, LexEntity
{
    use LexEntityJsonSerializer;

    public function __construct(
        public readonly ?string $description = null,
        public readonly LexObject | LexRef | LexRefUnion | null $schema = null,
    ) {
    }
}
