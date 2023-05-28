<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Types\LexBoolean;

use function is_bool;

class LexBooleanValidator implements Validator
{
    public function __construct(private readonly LexBoolean $type)
    {
    }

    public function validate(mixed $value, ?string $path = null): bool
    {
        if ($value === null) {
            $value = $this->type->default;
        }

        $path = $path ?? 'Value';

        if (!is_bool($value)) {
            throw new InvalidValue("$path must be a boolean");
        }

        if ($this->type->const !== null && $value !== $this->type->const) {
            throw new InvalidValue("$path must be " . ($this->type->const ? 'true' : 'false'));
        }

        return $value;
    }
}
