<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

abstract class LexPrimitive
{
    public function __construct(
        public readonly LexPrimitiveType $type,
        public readonly ?string $description,
    ) {
    }
}
