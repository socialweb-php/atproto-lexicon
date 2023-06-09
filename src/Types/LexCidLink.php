<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-type TLexCidLink = object{
 *     type: 'cid-link',
 *     description?: string,
 * }
 */
class LexCidLink implements JsonSerializable, LexUserType
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    public function __construct(public readonly ?string $description = null)
    {
        $this->type = LexType::CidLink;
    }
}
