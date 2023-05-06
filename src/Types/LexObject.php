<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type TLexArray from LexArray
 * @phpstan-import-type TLexBlob from LexBlob
 * @phpstan-import-type TLexIpldType from LexEntity
 * @phpstan-import-type TLexPrimitive from LexPrimitive
 * @phpstan-import-type TLexRefVariant from LexEntity
 * @phpstan-type TLexObject = object{
 *     type: 'object',
 *     description?: string,
 *     required?: string[],
 *     nullable?: string[],
 *     properties?: array<string, TLexArray | TLexBlob | TLexIpldType | TLexPrimitive | TLexRefVariant>,
 * }
 */
class LexObject implements LexUserType
{
    public readonly LexType $type;

    /**
     * @param string[] | null $required
     * @param string[] | null $nullable
     * @param array<string, LexArray | LexBlob | LexBytes | LexCidLink | LexPrimitive | LexRef | LexRefUnion> $properties
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?array $required = null,
        public readonly ?array $nullable = null,
        public readonly array $properties = [],
    ) {
        $this->type = LexType::Object;
    }
}
