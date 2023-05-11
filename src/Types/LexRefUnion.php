<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;

/**
 * @phpstan-type TLexRefUnion = object{
 *     type: 'union',
 *     description?: string,
 *     refs: list<string>,
 *     closed?: bool,
 * }
 */
class LexRefUnion implements JsonSerializable, LexEntity, LexResolvable
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    /**
     * @param list<string> $refs
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly array $refs = [],
        public readonly ?bool $closed = null,
        private readonly ?ParserFactory $parserFactory = null,
    ) {
        $this->type = LexType::Union;
    }

    public function resolve(): LexCollection
    {
        $resolved = [];

        foreach ($this->refs as $ref) {
            $ref = new LexRef(ref: $ref, parserFactory: $this->parserFactory);
            $ref->setParent($this);

            $resolved[] = $ref->resolve();
        }

        $collection = new LexCollection(...$resolved);
        $collection->setParent($this);

        return $collection;
    }
}
