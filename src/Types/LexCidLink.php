<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexBytesJson from LexBytes
 * @phpstan-type LexCidLinkJson = object{
 *     type: 'cid-link',
 *     description?: string,
 * }
 * @phpstan-type LexIpldTypeJson = LexBytesJson | LexCidLinkJson
 */
final class LexCidLink implements LexUserType
{
    public readonly LexType $type;

    public function __construct(public readonly ?string $description = null)
    {
        $this->type = LexType::CidLink;
    }
}
