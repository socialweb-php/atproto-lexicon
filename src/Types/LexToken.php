<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexTokenJson = object{
 *     type: 'token',
 *     description?: string,
 * }
 */
final class LexToken extends LexUserType
{
    public function __construct(?string $description = null)
    {
        parent::__construct(LexUserTypeType::Token, $description);
    }
}
