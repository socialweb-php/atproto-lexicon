<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-type TLexBytes = object{
 *     type: 'bytes',
 *     description?: string,
 *     maxLength?: int,
 *     minLength?: int,
 * }
 */
class LexBytes implements JsonSerializable, LexUserType
{
    use LexEntityJsonSerializer;

    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?int $maxLength = null,
        public readonly ?int $minLength = null,
    ) {
        $this->type = LexType::Bytes;
    }
}
