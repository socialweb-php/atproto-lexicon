<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexString extends LexPrimitive
{
    /**
     * @param string[] | null $enum
     * @param string[] | null $knownValues
     */
    public function __construct(
        public readonly ?string $format = null,
        public readonly ?string $default = null,
        public readonly ?int $minLength = null,
        public readonly ?int $maxLength = null,
        public readonly ?int $maxGraphemes = null,
        public readonly ?array $enum = null,
        public readonly ?string $const = null,
        public readonly ?array $knownValues = null,
        ?string $description = null,
    ) {
        parent::__construct(LexPrimitiveType::String, $description);
    }
}
