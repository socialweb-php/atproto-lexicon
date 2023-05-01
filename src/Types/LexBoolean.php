<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexBoolean extends LexPrimitive
{
    public function __construct(
        public readonly ?bool $default = null,
        public readonly ?bool $const = null,
        ?string $description = null,
    ) {
        parent::__construct(LexPrimitiveType::Boolean, $description);
    }
}
