<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexNumber extends LexPrimitive
{
    /**
     * @param list<float | int> | null $enum
     */
    public function __construct(
        public readonly float | int | null $default = null,
        public readonly float | int | null $minimum = null,
        public readonly float | int | null $maximum = null,
        public readonly ?array $enum = null,
        public readonly float | int | null $const = null,
        ?string $description = null,
    ) {
        parent::__construct(LexPrimitiveType::Number, $description);
    }
}
