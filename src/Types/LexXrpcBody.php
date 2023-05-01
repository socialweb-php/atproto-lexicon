<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexXrpcBody
{
    public function __construct(
        public readonly string $encoding,
        public readonly LexObject $schema,
        public readonly ?string $description = null,
    ) {
    }
}
