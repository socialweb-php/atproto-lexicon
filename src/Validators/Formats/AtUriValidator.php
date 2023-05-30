<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators\Formats;

use SocialWeb\Atproto\Lexicon\Nsid\InvalidNsid;
use SocialWeb\Atproto\Lexicon\Nsid\NsidValidator;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function count;
use function explode;
use function is_string;
use function preg_match;
use function strlen;

class AtUriValidator implements Validator
{
    public function validate(mixed $value, ?string $path = null): string
    {
        if (!is_string($value)) {
            throw new InvalidValue('AT URI must be a string');
        }

        $uriParts = explode('#', $value);

        if (count($uriParts) > 2) {
            throw new InvalidValue('AT URI can have at most one hash mark (#)');
        }

        $fragmentPart = $uriParts[1] ?? null;
        $uri = $uriParts[0];

        if (!preg_match("#^[a-zA-Z0-9._~:@!$&')(*+,;=%/-]*$#", $uri)) {
            throw new InvalidValue('Invalid characters found in AT URI');
        }

        $parts = explode('/', $uri);

        if (count($parts) >= 3 && ($parts[0] !== 'at:' || strlen($parts[1]) !== 0)) {
            throw new InvalidValue('AT URI must use "at://" scheme');
        }

        if (count($parts) < 3) {
            throw new InvalidValue('AT URI requires at least method and authority sections');
        }

        try {
            (new HandleValidator())->validate($parts[2]);
        } catch (InvalidHandle) {
            try {
                (new DidValidator())->validate($parts[2]);
            } catch (InvalidDid) {
                throw new InvalidValue('AT URI authority must be a valid handle or DID');
            }
        }

        if (count($parts) >= 4) {
            if (strlen($parts[3]) === 0) {
                throw new InvalidValue('AT URI cannot have a slash after the authority without a path segment');
            }

            try {
                (new NsidValidator())->validate($parts[3]);
            } catch (InvalidNsid) {
                throw new InvalidValue('AT URI requires valid NSID as first path segment (if provided)');
            }
        }

        if (count($parts) >= 5 && strlen($parts[4]) === 0) {
            throw new InvalidValue('AT URI cannot have a slash after the collection unless a record key is provided');
        }

        if (count($parts) >= 6) {
            throw new InvalidValue('AT URI path can have at most two parts and no trailing slash');
        }

        if ($fragmentPart !== null) {
            if (strlen($fragmentPart) === 0 || $fragmentPart[0] !== '/') {
                throw new InvalidValue('AT URI fragment must be non-empty and start with slash');
            }

            if (!preg_match("#^/[a-zA-Z0-9._~:@!$&')(*+,;=%[\]/-]*$#", $fragmentPart)) {
                throw new InvalidValue('Invalid characters found in AT URI fragment');
            }
        }

        if (strlen($uri) > 8192) {
            throw new InvalidValue('AT URI cannot be longer than 8 KB');
        }

        return $value;
    }
}
