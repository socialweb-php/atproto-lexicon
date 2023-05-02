<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexiconDoc implements LexType
{
    public readonly int $lexicon;

    /**
     * @param string $id An NSID
     * @param array<string, LexType> $defs
     */
    public function __construct(
        public readonly string $id,
        public readonly array $defs,
        public readonly float | int | null $revision = null,
        public readonly ?string $description = null,
    ) {
        $this->lexicon = 1;
    }
}
