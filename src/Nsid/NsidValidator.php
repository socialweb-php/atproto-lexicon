<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Nsid;

use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function array_slice;
use function count;
use function explode;
use function implode;
use function is_string;
use function preg_match;
use function str_ends_with;
use function strlen;

class NsidValidator implements Validator
{
    /**
     * @throws InvalidNsid
     */
    public function validate(mixed $value, ?string $path = null): string
    {
        if (!is_string($value)) {
            throw new InvalidNsid('NSID must be a string');
        }

        $parts = explode('.', $value);
        $toCheck = array_slice($parts, -1) === ['*']
            ? implode('.', array_slice($parts, 0, -1))
            : implode('.', $parts);

        if (!preg_match('/^[a-zA-Z0-9.-]*$/', $toCheck)) {
            throw new InvalidNsid('Invalid characters found in NSID');
        }

        if (strlen($toCheck) > 382) {
            throw new InvalidNsid('NSID cannot be longer than 382 characters');
        }

        $labels = explode('.', $toCheck);
        $labelCount = count($labels);

        if ($labelCount < 3) {
            throw new InvalidNsid('NSID needs at least three parts');
        }

        for ($i = 0; $i < $labelCount; $i++) {
            $this->ensureValidLabel($labels[$i], $i + 1 < $labelCount, $i + 1 === $labelCount);
        }

        return $value;
    }

    private function ensureValidLabel(
        string $label,
        bool $isDomainPart,
        bool $isNamePart,
    ): void {
        if ($label === '') {
            throw new InvalidNsid('NSID parts cannot be empty');
        }

        if (strlen($label) > 63 && $isDomainPart) {
            throw new InvalidNsid('NSID domain part cannot be longer than 63 characters');
        }

        if (strlen($label) > 128 && $isNamePart) {
            throw new InvalidNsid('NSID name part cannot be longer than 128 characters');
        }

        if (str_ends_with($label, '-')) {
            throw new InvalidNsid('NSID parts cannot end with a hyphen');
        }

        if (!preg_match('/^[a-zA-Z]/', $label)) {
            throw new InvalidNsid('NSID parts must start with an ASCII letter');
        }
    }
}
