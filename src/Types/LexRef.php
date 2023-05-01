<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexRef
{
    public readonly LexPrimitiveType $type;

    public function __construct(
        public readonly string $ref,
    ) {
        $this->type = LexPrimitiveType::Ref;
    }
}
