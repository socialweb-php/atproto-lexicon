<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;

/**
 * @phpstan-import-type TLexUserType from LexUserType
 * @phpstan-import-type TNsid from Nsid
 * @phpstan-type TLexiconDoc = object{
 *     lexicon: 1,
 *     id: TNsid,
 *     revision?: float | int,
 *     description?: string,
 *     defs: array<string, TLexUserType>,
 * }
 */
class LexiconDoc implements JsonSerializable, LexEntity
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public const MAIN = 'main';

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

        foreach ($this->defs as $def) {
            $def->setParent($this);
        }
    }
}
