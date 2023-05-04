<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexObject extends LexUserType
{
    /**
     * @param string[] | null $required
     * @param array<string, LexArray | LexBlob | LexObject | LexPrimitive | LexRef | LexRefUnion | LexUnknown> $properties
     */
    public function __construct(
        public readonly array $properties = [],
        public readonly ?array $required = null,
        ?string $description = null,
    ) {
        parent::__construct(LexUserTypeType::Object, $description);
    }
}
