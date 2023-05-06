<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexUnknownJson = object{
 *     type: 'unknown',
 *     description?: string,
 * }
 */
final class LexUnknown extends LexUserType
{
    public function __construct(?string $description = null)
    {
        parent::__construct(LexUserTypeType::Unknown, $description);
    }
}
