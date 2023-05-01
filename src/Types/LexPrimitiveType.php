<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

enum LexPrimitiveType: string
{
    case Array = 'array';
    case Boolean = 'boolean';
    case Integer = 'integer';
    case Number = 'number';
    case Ref = 'ref';
    case String = 'string';
    case Union = 'union';
}
