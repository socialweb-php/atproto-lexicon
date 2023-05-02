<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexUnion implements LexType
{
    public readonly LexPrimitiveType $type;

    /**
     * @param string[] | array<LexType> $refs
     */
    public function __construct(
        public readonly array $refs,
    ) {
        $this->type = LexPrimitiveType::Union;
    }
}
