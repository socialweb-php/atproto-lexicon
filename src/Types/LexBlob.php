<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexBlob extends LexUserType
{
    /**
     * @param string[] | null $accept
     */
    public function __construct(
        public readonly ?array $accept = null,
        public readonly float | int | null $maxSize = null,
        ?string $description = null,
    ) {
        parent::__construct(LexUserTypeType::Blob, $description);
    }
}
