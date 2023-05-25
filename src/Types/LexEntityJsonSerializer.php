<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use function array_filter;
use function array_walk;
use function in_array;

trait LexEntityJsonSerializer
{
    /**
     * These properties are key-value pair arrays that are converted to JSON
     * objects when serialized as JSON. If they are empty, they must be
     * converted to empty objects for proper serialization.
     */
    private const OBJECT_PROPS = ['defs', 'refs', 'properties'];

    public function jsonSerialize(): object
    {
        $objectifyProps = function (mixed &$value, string $key): void {
            if (in_array($key, self::OBJECT_PROPS) && $value === []) {
                $value = (object) [];
            }
        };

        $properties = (array) $this;
        array_walk($properties, $objectifyProps);

        return (object) array_filter($properties, fn (mixed $v): bool => $v !== null);
    }
}
