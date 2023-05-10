<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexEntityParent;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class LexEntityParentTest extends TestCase
{
    use LexEntityParent;

    public function testGetParentReturnsNull(): void
    {
        $this->assertNull($this->getParent());
    }

    public function testSetParent(): void
    {
        $entity = $this->mockery(LexEntity::class);
        $this->setParent($entity);

        $this->assertSame($entity, $this->getParent());
    }
}
