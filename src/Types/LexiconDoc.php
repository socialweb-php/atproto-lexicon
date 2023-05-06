<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use SocialWeb\Atproto\Lexicon\Nsid\Nsid;

/**
 * @phpstan-import-type LexUserTypeJson from LexUserType
 * @phpstan-import-type NsidJson from Nsid
 * @phpstan-type LexiconDocJson = object{
 *     lexicon: 1,
 *     id: NsidJson,
 *     revision?: float | int,
 *     description?: string,
 *     defs: array<string, LexUserTypeJson>,
 * }
 */
class LexiconDoc implements LexEntity
{
    public readonly int $lexicon;

    /**
     * @param array<string, LexEntity> $defs
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
