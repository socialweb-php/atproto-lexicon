<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-type TLexRefUnion = object{
 *     type: 'union',
 *     description?: string,
 *     refs: list<string>,
 *     closed?: bool,
 * }
 */
class LexRefUnion implements JsonSerializable, LexEntity
{
    use LexEntityJsonSerializer;

    public readonly LexType $type;

    /**
     * @param list<string> $refs
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly array $refs = [],
        public readonly ?bool $closed = null,
    ) {
        $this->type = LexType::Union;
    }
}
