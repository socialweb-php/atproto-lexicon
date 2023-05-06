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
class LexBlob implements LexUserType
{
    public readonly LexType $type;

    /**
     * @param string[] | null $accept
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?array $accept = null,
        public readonly float | int | null $maxSize = null,
    ) {
        $this->type = LexType::Blob;
    }
}
