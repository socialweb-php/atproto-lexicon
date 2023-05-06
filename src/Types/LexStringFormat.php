<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type TLexStringFormat = 'at-identifier' | 'at-uri' | 'cid' | 'datetime' | 'did' | 'handle' | 'nsid' | 'uri'
 */
enum LexStringFormat: string
{
    case AtIdentifier = 'at-identifier';
    case AtUri = 'at-uri';
    case Cid = 'cid';
    case DateTime = 'datetime';
    case Did = 'did';
    case Handle = 'handle';
    case Nsid = 'nsid';
    case Uri = 'uri';
}
