<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators\Formats;

use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function is_string;
use function preg_match;

class UriValidator implements Validator
{
    /**
     * @throws InvalidValue
     */
    public function validate(mixed $value, ?string $path = null): string
    {
        $path = $path ?? 'Value';

        if (!is_string($value) || !preg_match('#^\w+:(?://)?\S+$#', $value)) {
            throw new InvalidValue("$path must be a URI");
        }

        return $value;
    }
}
