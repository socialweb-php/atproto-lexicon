<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

use Traversable;
use stdClass;

use function array_filter;
use function array_keys;
use function count;
use function get_object_vars;
use function is_array;
use function is_string;

trait DictionaryValidation
{
    /**
     * @phpstan-assert-if-true iterable<string, mixed> | object $value
     */
    private function isDictionary(mixed $value): bool
    {
        if ($value instanceof stdClass) {
            $value = get_object_vars($value);
        }

        if ($value instanceof Traversable) {
            $value = $this->traversableToArray($value);
        }

        if (!is_array($value)) {
            return false;
        }

        return count(array_filter(array_keys($value), is_string(...))) === count($value);
    }

    /**
     * @param Traversable<mixed> $traversable
     *
     * @return mixed[]
     */
    private function traversableToArray(Traversable $traversable): array
    {
        $array = [];

        /**
         * @var array-key $key
         * @var mixed $value
         */
        foreach ($traversable as $key => $value) {
            /** @psalm-suppress MixedAssignment */
            $array[$key] = $value;
        }

        return $array;
    }
}
