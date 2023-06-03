<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators\Formats;

use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function is_string;

class CidValidator implements Validator
{
    public function validate(mixed $value, ?string $path = null): string
    {
        $path = $path ?? 'Value';

        // Need to find a PHP Multiformats library to validate CIDs.
        if (!is_string($value) || $value === '') {
            throw new InvalidValue("$path must be a CID string");
        }

        return $value;
    }
}
