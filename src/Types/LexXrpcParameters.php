<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-import-type TLexPrimitive from LexPrimitive
 * @phpstan-import-type TLexPrimitiveArray from LexPrimitiveArray
 * @phpstan-type TLexXrpcParameters = object{
 *     type: 'params',
 *     description?: string,
 *     required?: list<string>,
 *     properties: array<string, TLexPrimitive | TLexPrimitiveArray>,
 * }
 */
class LexXrpcParameters implements JsonSerializable, LexEntity
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    /**
     * @param list<string> | null $required
     * @param array<string, LexPrimitive | LexPrimitiveArray> $properties
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?array $required = null,
        public readonly array $properties = [],
    ) {
        $this->type = LexType::Params;

        foreach ($this->properties as $property) {
            $property->setParent($this);
        }
    }
}
