<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use function array_reduce;
use function is_array;
use function is_float;
use function is_int;
use function is_string;

trait IsArrayOf
{
    private function isArrayOfNumber(mixed $value): bool
    {
        return is_array($value)
            && array_reduce($value, fn (bool $c, mixed $i): bool => $c && (is_int($i) || is_float($i)), true);
    }

    private function isArrayOfInteger(mixed $value): bool
    {
        return is_array($value) && array_reduce($value, fn (bool $c, mixed $i): bool => $c && is_int($i), true);
    }

    private function isArrayOfString(mixed $value): bool
    {
        return is_array($value) && array_reduce($value, fn (bool $c, mixed $i): bool => $c && is_string($i), true);
    }
}
