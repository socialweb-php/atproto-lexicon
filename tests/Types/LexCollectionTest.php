<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use BadMethodCallException;
use SocialWeb\Atproto\Lexicon\Types\LexCollection;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class LexCollectionTest extends TestCase
{
    public function testLexCollection(): void
    {
        $entity1 = $this->mockery(LexEntity::class);
        $entity2 = $this->mockery(LexEntity::class);
        $entity3 = $this->mockery(LexEntity::class);

        $collection = new LexCollection($entity1, $entity2, $entity3);

        $this->assertCount(3, $collection);
        $this->assertContainsOnlyInstancesOf(LexEntity::class, $collection);
        $this->assertTrue(isset($collection[1]));
        $this->assertSame($entity2, $collection[1]);
    }

    public function testThrowsExceptionWhenAttemptingToSet(): void
    {
        $collection = new LexCollection();
        $entity = $this->mockery(LexEntity::class);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot modify immutable collection ' . LexCollection::class);

        $collection[] = $entity;
    }

    public function testThrowsExceptionWhenAttemptingToUnset(): void
    {
        $entity = $this->mockery(LexEntity::class);
        $collection = new LexCollection($entity);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot modify immutable collection ' . LexCollection::class);

        unset($collection[0]);
    }
}
