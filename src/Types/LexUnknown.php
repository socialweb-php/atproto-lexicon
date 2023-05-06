<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexUnknownJson = object{
 *     type: 'unknown',
 *     description?: string,
 * }
 */
final class LexUnknown implements LexPrimitive, LexUserType
{
    public readonly LexType $type;

    public function __construct(public readonly ?string $description = null)
    {
        $this->type = LexType::Unknown;
    }
}
