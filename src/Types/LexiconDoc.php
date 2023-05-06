<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use SocialWeb\Atproto\Lexicon\Nsid\Nsid;

/**
 * @phpstan-type LexiconDocJson = object{
 *     lexicon: 1,
 *     id: string,
 *     revision?: float | int,
 *     description?: string,
 *     defs: array<string, object>,
 * }
 */
final class LexiconDoc implements LexType
{
    public readonly int $lexicon;

    /**
     * @param array<string, LexType> $defs
     */
    public function __construct(
        public readonly Nsid $id,
        public readonly float | int | null $revision = null,
        public readonly ?string $description = null,
        public readonly array $defs = [],
    ) {
        $this->lexicon = 1;
    }
}
