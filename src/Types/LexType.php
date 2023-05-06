<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

enum LexType: string
{
    case Array = 'array';
    case Blob = 'blob';
    case Boolean = 'boolean';
    case Integer = 'integer';
    case Object = 'object';
    case Procedure = 'procedure';
    case Query = 'query';
    case Record = 'record';
    case Ref = 'ref';
    case String = 'string';
    case Token = 'token';
    case Union = 'union';
    case Unknown = 'unknown';
}
