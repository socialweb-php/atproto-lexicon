<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Types\LexBytes;

use function is_string;
use function strlen;

class LexBytesValidator implements Validator
{
    public function __construct(private readonly LexBytes $type)
    {
    }

    public function validate(mixed $value, ?string $path = null): string
    {
        $path = $path ?? 'Value';

        if (!is_string($value) || $value === '') {
            throw new InvalidValue("$path must be a byte string");
        }

        if ($this->type->maxLength !== null && strlen($value) > $this->type->maxLength) {
            throw new InvalidValue("$path must not be larger than {$this->type->maxLength} bytes");
        }

        if ($this->type->minLength !== null && strlen($value) < $this->type->minLength) {
            throw new InvalidValue("$path must not be smaller than {$this->type->minLength} bytes");
        }

        return $value;
    }
}
