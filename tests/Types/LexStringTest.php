<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexResolvable;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexToken;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class LexStringTest extends TestCase
{
    public function testResolvesKnownValues(): void
    {
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new DefaultParserFactory($schemaRepository);

        $lexRef = new LexRef(
            ref: 'org.example.foo.getSomething#reasonType',
            parserFactory: $parserFactory,
        );

        $lexString = $lexRef->resolve();
        $this->assertInstanceOf(LexString::class, $lexString);
        $this->assertInstanceOf(LexResolvable::class, $lexString);

        $stringParent = $lexString->getParent();
        $this->assertInstanceOf(LexiconDoc::class, $stringParent);
        $this->assertSame('org.example.foo.getSomething', $stringParent->id->nsid);

        $lexCollection = $lexString->resolve();

        $this->assertSame($lexString, $lexCollection->getParent());
        $this->assertCount(6, $lexCollection);
        $this->assertContainsOnlyInstancesOf(LexToken::class, $lexCollection);

        foreach ($lexCollection as $entity) {
            $this->assertSame($stringParent, $entity->getParent());
        }
    }
}
