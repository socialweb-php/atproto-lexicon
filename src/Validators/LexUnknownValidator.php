<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

class LexUnknownValidator implements Validator
{
    use DictionaryValidation;

    /**
     * @return iterable<string, mixed> | object
     */
    public function validate(mixed $value, ?string $path = null): iterable | object
    {
        $path = $path ?? 'Value';

        if (!$this->isDictionary($value)) {
            throw new InvalidValue(
                "$path must be a dictionary of key-value pairs, i.e., a JSON object",
            );
        }

        return $value;
    }
}
