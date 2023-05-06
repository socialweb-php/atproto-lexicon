<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type TLexRefUnion = object{
 *     type: 'union',
 *     description?: string,
 *     refs: string[],
 *     closed?: bool,
 * }
 */
class LexRefUnion implements LexEntity
{
    public readonly LexType $type;

    /**
     * @param string[] $refs
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly array $refs = [],
        public readonly ?bool $closed = null,
    ) {
        $this->type = LexType::Union;
    }
}
