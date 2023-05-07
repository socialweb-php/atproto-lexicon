<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-type TLexRef = object{
 *     type: 'ref',
 *     description?: string,
 *     ref: string,
 * }
 */
class LexRef implements JsonSerializable, LexEntity
{
    use LexEntityJsonSerializer;

    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?string $ref = null,
    ) {
        $this->type = LexType::Ref;
    }
}
