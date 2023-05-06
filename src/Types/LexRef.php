<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type TLexRef = object{
 *     type: 'ref',
 *     description?: string,
 *     ref: string,
 * }
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
