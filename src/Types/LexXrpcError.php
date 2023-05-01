<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexXrpcError
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }
}
