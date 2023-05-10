<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;

/**
 * @phpstan-import-type TLexStringFormat from LexStringFormat
 * @phpstan-type TLexString = object{
 *     type: 'string',
 *     format?: TLexStringFormat,
 *     description?: string,
 *     default?: string,
 *     minLength?: int,
 *     maxLength?: int,
 *     minGraphemes?: int,
 *     maxGraphemes?: int,
 *     enum?: list<string>,
 *     const?: string,
 *     knownValues?: list<string>,
 * }
 */
class LexString implements JsonSerializable, LexPrimitive, LexUserType
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    /**
     * @param list<string> | null $enum
     * @param list<string> | null $knownValues
     */
    public function __construct(
        public readonly ?LexStringFormat $format = null,
        public readonly ?string $description = null,
        public readonly ?string $default = null,
        public readonly ?int $minLength = null,
        public readonly ?int $maxLength = null,
        public readonly ?int $minGraphemes = null,
        public readonly ?int $maxGraphemes = null,
        public readonly ?array $enum = null,
        public readonly ?string $const = null,
        public readonly ?array $knownValues = null,
        private readonly ?ParserFactory $parserFactory = null,
    ) {
        $this->type = LexType::String;
    }

    /**
     * Converts the string refs of the known values to {@see LexRef} instances,
     * which may then be resolved for further processing.
     *
     * @return list<LexRef>
     */
    public function getLexRefsForKnownValues(): array
    {
        $lexRefs = [];

        foreach ($this->knownValues ?? [] as $ref) {
            $lexRefs[] = new LexRef($this->description, $ref, $this->parserFactory);
        }

        return $lexRefs;
    }
}
