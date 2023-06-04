<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators\Formats;

use SocialWeb\Atproto\Lexicon\Nsid\InvalidNsid;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

class NsidValidator implements Validator
{
    public function validate(mixed $value, ?string $path = null): string
    {
        $path = $path ?? 'Value';

        try {
            $value = (new \SocialWeb\Atproto\Lexicon\Nsid\NsidValidator())->validate($value);
        } catch (InvalidNsid $exception) {
            throw new InvalidValue("$path must be a valid NSID", previous: $exception);
        }

        return $value;
    }
}
