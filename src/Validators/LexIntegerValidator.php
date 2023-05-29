<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Types\LexInteger;

use function implode;
use function in_array;
use function is_int;

class LexIntegerValidator implements Validator
{
    public function __construct(private readonly LexInteger $type)
    {
    }

    public function validate(mixed $value, ?string $path = null): int
    {
        if ($value === null) {
            $value = $this->type->default;
        }

        $path = $path ?? 'Value';

        if (!is_int($value)) {
            throw new InvalidValue("$path must be an integer");
        }

        if ($this->type->const !== null && $value !== $this->type->const) {
            throw new InvalidValue("$path must be {$this->type->const}");
        }

        if ($this->type->enum !== null && !in_array($value, $this->type->enum)) {
            throw new InvalidValue("$path must be one of (" . implode('|', $this->type->enum) . ')');
        }

        if ($this->type->maximum !== null && $value > $this->type->maximum) {
            throw new InvalidValue("$path cannot be greater than {$this->type->maximum}");
        }

        if ($this->type->minimum !== null && $value < $this->type->minimum) {
            throw new InvalidValue("$path cannot be less than {$this->type->minimum}");
        }

        return $value;
    }
}
