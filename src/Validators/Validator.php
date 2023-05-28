<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

interface Validator
{
    /**
     * Validates a value, returning the value if it is valid and throwing an
     * InvalidValue exception otherwise.
     *
     * For schemas that define default values, if the value provided to this
     * method is `null`, it should return the default value.
     *
     * @param string | null $path The JSON path in the source JSON to the
     *     provided value, if available. This is primarily useful for helpful
     *     exception messages.
     *
     * @throws InvalidValue if the value fails validation.
     */
    public function validate(mixed $value, ?string $path = null): mixed;
}
