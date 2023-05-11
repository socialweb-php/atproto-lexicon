<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

interface LexResolvable
{
    public function resolve(): LexEntity;
}
