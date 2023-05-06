<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexTokenJson = object{
 *     type: 'token',
 *     description?: string,
 * }
 */
class LexToken implements LexUserType
{
    public readonly LexType $type;

    public function __construct(public readonly ?string $description = null)
    {
        $this->type = LexType::Token;
    }
}
