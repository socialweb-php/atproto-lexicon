<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-type TLexInteger = object{
 *     type: 'integer',
 *     description?: string,
 *     default?: int,
 *     minimum?: int,
 *     maximum?: int,
 *     enum?: list<int>,
 *     const?: int,
 * }
 */
class LexInteger implements JsonSerializable, LexPrimitive, LexUserType
{
    use LexEntityJsonSerializer;

    public readonly LexType $type;

    /**
     * @param list<int> | null $enum
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?int $default = null,
        public readonly ?int $minimum = null,
        public readonly ?int $maximum = null,
        public readonly ?array $enum = null,
        public readonly ?int $const = null,
    ) {
        $this->type = LexType::Integer;
    }
}
