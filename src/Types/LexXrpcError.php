<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexXrpcErrorJson = object{
 *     name: string,
 *     description?: string,
 * }
 */
class LexXrpcError implements LexEntity
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }
}
