<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexObjectJson = object{
 *     type: 'object',
 *     description?: string,
 *     required?: string[],
 *     nullable?: string[],
 *     properties?: array<string, object>,
 * }
 */
final class LexObject extends LexUserType
{
    /**
     * @param string[] | null $required
     * @param string[] | null $nullable
     * @param array<string, LexArray | LexBlob | LexObject | LexPrimitive | LexRef | LexRefUnion | LexUnknown> $properties
     */
    public function __construct(
        ?string $description = null,
        public readonly ?array $required = null,
        public readonly ?array $nullable = null,
        public readonly array $properties = [],
    ) {
        parent::__construct(LexUserTypeType::Object, $description);
    }
}
