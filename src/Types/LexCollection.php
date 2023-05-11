<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Countable;
use IteratorAggregate;
use Traversable;

use function array_values;
use function count;

/**
 * @implements ArrayAccess<int, LexEntity>
 * @implements IteratorAggregate<int, LexEntity>
 */
class LexCollection implements ArrayAccess, Countable, IteratorAggregate, LexEntity
{
    use LexEntityParent;

    /**
     * @var array<LexEntity>
     */
    private readonly array $entities;

    public function __construct(LexEntity ...$entities)
    {
        $this->entities = array_values($entities);
    }

    public function count(): int
    {
        return count($this->entities);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->entities);
    }

    public function offsetExists(mixed $offset): bool
    {
        /** @psalm-suppress MixedArrayOffset */
        return isset($this->entities[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        /** @psalm-suppress MixedArrayOffset */
        return $this->entities[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new BadMethodCallException('Cannot modify immutable collection ' . self::class);
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new BadMethodCallException('Cannot modify immutable collection ' . self::class);
    }
}
