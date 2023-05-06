<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexBlobJson = object{
 *     type: 'blob',
 *     description?: string,
 *     accept?: string[],
 *     maxSize?: int | float,
 * }
 */
final class LexBlob extends LexUserType
{
    /**
     * @param string[] | null $accept
     */
    public function __construct(
        ?string $description = null,
        public readonly ?array $accept = null,
        public readonly float | int | null $maxSize = null,
    ) {
        parent::__construct(LexUserTypeType::Blob, $description);
    }
}
