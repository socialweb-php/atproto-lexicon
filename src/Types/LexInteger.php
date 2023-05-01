<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexInteger extends LexPrimitive
{
    /**
     * @param int[] | null $enum
     */
    public function __construct(
        public readonly ?int $default = null,
        public readonly ?int $minimum = null,
        public readonly ?int $maximum = null,
        public readonly ?array $enum = null,
        public readonly ?int $const = null,
        ?string $description = null,
    ) {
        parent::__construct(LexPrimitiveType::Integer, $description);
    }
}
