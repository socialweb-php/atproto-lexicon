<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators\Formats;

use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function assert;
use function is_string;

class AtIdentifierValidator implements Validator
{
    /**
     * @throws InvalidValue
     */
    public function validate(mixed $value, ?string $path = null): string
    {
        $path = $path ?? 'Value';

        try {
            (new DidValidator())->validate($value);
        } catch (InvalidDid) {
            try {
                (new HandleValidator())->validate($value);
            } catch (InvalidHandle) {
                throw new InvalidValue("$path must be a valid handle or DID");
            }
        }

        assert(is_string($value));

        return $value;
    }
}
