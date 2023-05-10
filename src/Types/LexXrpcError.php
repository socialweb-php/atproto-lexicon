<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-type TLexXrpcError = object{
 *     name: string,
 *     description?: string,
 * }
 */
class LexXrpcError implements JsonSerializable, LexEntity
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }
}
