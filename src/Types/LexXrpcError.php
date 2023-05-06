<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexXrpcErrorJson = object{
 *     name: string,
 *     description?: string,
 * }
 */
final class LexXrpcError implements LexType
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }
}
