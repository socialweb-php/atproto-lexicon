<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

abstract class LexUserType
{
    public function __construct(
        public readonly LexUserTypeType $type,
        public readonly ?string $description,
    ) {
    }
}
