<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexRefUnion implements LexType
{
    public readonly LexPrimitiveType $type;

    /**
     * @param string[] $refs
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly array $refs = [],
        public readonly ?bool $closed = null,
    ) {
        $this->type = LexPrimitiveType::Union;
    }
}
