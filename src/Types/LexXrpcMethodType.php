<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

enum LexXrpcMethodType: string
{
    case Query = 'query';
    case Procedure = 'procedure';
}