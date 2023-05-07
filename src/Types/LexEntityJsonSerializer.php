<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use function array_filter;

trait LexEntityJsonSerializer
{
    public function jsonSerialize(): object
    {
        return (object) array_filter((array) $this, fn (mixed $v): bool => $v !== null);
    }
}
