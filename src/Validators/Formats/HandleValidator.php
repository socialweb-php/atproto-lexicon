<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators\Formats;

use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function array_pop;
use function count;
use function explode;
use function is_string;
use function preg_match;
use function str_ends_with;
use function str_starts_with;
use function strlen;

class HandleValidator implements Validator
{
    /**
     * @throws InvalidHandle
     */
    public function validate(mixed $value, ?string $path = null): string
    {
        if (!is_string($value)) {
            throw new InvalidHandle('Handle must be a string');
        }

        if (!preg_match('/^[a-zA-Z0-9.-]*$/', $value)) {
            throw new InvalidHandle('Invalid characters found in handle');
        }

        if (strlen($value) > 253) {
            throw new InvalidHandle('A handle cannot be longer than 253 characters');
        }

        $labels = explode('.', $value);

        if (count($labels) < 2) {
            throw new InvalidHandle('A handle domain requires at least 2 parts');
        }

        foreach ($labels as $label) {
            self::ensureValidLabel($label);
        }

        if (!preg_match('/^[a-zA-Z]/', array_pop($labels))) {
            throw new InvalidHandle('A handle\'s final component (TLD) must start with an ASCII letter');
        }

        return $value;
    }

    private function ensureValidLabel(string $label): void
    {
        if ($label === '') {
            throw new InvalidHandle('Handle parts cannot be empty');
        }

        if (strlen($label) > 63) {
            throw new InvalidHandle('A handle part cannot be longer than 63 characters');
        }

        if (str_starts_with($label, '-') || str_ends_with($label, '-')) {
            throw new InvalidHandle('Handle parts cannot start or end with hyphens');
        }
    }
}
