<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

use function is_string;

class LexCidLinkValidator implements Validator
{
    public function validate(mixed $value, ?string $path = null): string
    {
        $path = $path ?? 'Value';

        // Need to find a PHP Multiformats library to validate CIDs.
        if (!is_string($value) || $value === '') {
            throw new InvalidValue("$path must be a CID");
        }

        return $value;
    }
}
