<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

enum LexUserTypeType: string
{
    case Blob = 'blob';
    case Object = 'object';
    case Procedure = 'procedure';
    case Query = 'query';
    case Record = 'record';
    case Token = 'token';
    case Unknown = 'unknown';
}
