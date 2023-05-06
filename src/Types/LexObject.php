<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexArrayJson from LexArray
 * @phpstan-import-type LexBlobJson from LexBlob
 * @phpstan-import-type LexIpldTypeJson from LexCidLink
 * @phpstan-import-type LexPrimitiveJson from LexPrimitive
 * @phpstan-import-type LexRefVariantJson from LexRef
 * @phpstan-type LexObjectJson = object{
 *     type: 'object',
 *     description?: string,
 *     required?: string[],
 *     nullable?: string[],
 *     properties?: array<string, LexArrayJson | LexBlobJson | LexIpldTypeJson | LexPrimitiveJson | LexRefVariantJson>,
 * }
 */
final class LexObject implements LexUserType
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
