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
        private readonly ?ParserFactory $parserFactory = null,
    ) {
        $this->type = LexType::Union;
    }

    /**
     * Converts the string refs of the union to {@see LexRef} instances, which
     * may then be resolved for further processing.
     *
     * @return list<LexRef>
     */
    public function getLexRefs(): array
    {
        $lexRefs = [];

        foreach ($this->refs as $ref) {
            $lexRefs[] = new LexRef($this->description, $ref, $this->parserFactory);
        }

        return $lexRefs;
    }
}
