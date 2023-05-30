<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators\Formats;

use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function count;
use function explode;
use function is_string;
use function preg_match;
use function str_ends_with;
use function strlen;

class DidValidator implements Validator
{
    /**
     * @throws InvalidDid
     */
    public function validate(mixed $value, ?string $path = null): string
    {
        if (!is_string($value)) {
            throw new InvalidDid('DID must be a string');
        }

        if (!preg_match('/^[a-zA-Z0-9._:%-]*$/', $value)) {
            throw new InvalidDid('Invalid characters found in DID');
        }

        $parts = explode(':', $value);

        if (count($parts) < 3) {
            throw new InvalidDid('DID requires a prefix, method, and method-specific content');
        }

        if ($parts[0] !== 'did') {
            throw new InvalidDid('DID requires "did:" prefix');
        }

        if (!preg_match('/^[a-z]+$/', $parts[1])) {
            throw new InvalidDid('DID method must use only lower-case letters');
        }

        if (str_ends_with($value, ':') || str_ends_with($value, '%')) {
            throw new InvalidDid('DID cannot end with ":" or "%" characters');
        }

        if (strlen($value) > 8192) {
            throw new InvalidDid('DID cannot be longer than 8 KB');
        }

        return $value;
    }
}
