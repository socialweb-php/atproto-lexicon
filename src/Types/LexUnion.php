<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexUnion
{
    public readonly LexPrimitiveType $type;

    /**
     * @param string[] | array<LexArray | LexPrimitive | LexRef | LexUnion | LexUserType> $refs
     */
    public function __construct(
        public readonly array $refs,
    ) {
        $this->type = LexPrimitiveType::Union;
    }
}
