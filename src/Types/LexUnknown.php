<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-type TLexUnknown = object{
 *     type: 'unknown',
 *     description?: string,
 * }
 */
class LexUnknown implements JsonSerializable, LexPrimitive, LexUserType
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    public function __construct(public readonly ?string $description = null)
    {
        $this->type = LexType::Unknown;
    }
}
