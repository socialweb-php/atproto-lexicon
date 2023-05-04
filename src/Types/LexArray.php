<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexArray implements LexType
{
    public readonly LexPrimitiveType $type;

    public function __construct(
        public readonly LexObject | LexPrimitive | LexRef | LexUnion | LexUnknown | null $items = null,
        public readonly ?int $minLength = null,
        public readonly ?int $maxLength = null,
        public readonly ?string $description = null,
    ) {
        $this->type = LexPrimitiveType::Array;
    }
}
