<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

enum LexUserTypeType: string
{
    case Audio = 'audio';
    case Blob = 'blob';
    case Image = 'image';
    case Object = 'object';
    case Procedure = 'procedure';
    case Query = 'query';
    case Record = 'record';
    case Token = 'token';
    case Unknown = 'unknown';
    case Video = 'video';
}
