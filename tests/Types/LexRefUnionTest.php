<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;
use SocialWeb\Atproto\Lexicon\Types\LexToken;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class LexRefUnionTest extends TestCase
{
    public function testGetLexRefs(): void
    {
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new DefaultParserFactory($schemaRepository);

        $lexRefUnion = new LexRefUnion(
            refs: [
                'org.example.foo.getSomething',
                'org.example.foo.getSomething#actionReversal',
                'org.example.foo.getSomething#takedown',
            ],
            parserFactory: $parserFactory,
        );

        $lexRefs = $lexRefUnion->getLexRefs();

        $this->assertCount(3, $lexRefs);
        $this->assertContainsOnlyInstancesOf(LexRef::class, $lexRefs);

        $entity1 = $lexRefs[0]->resolve();
        $entity2 = $lexRefs[1]->resolve();
        $entity3 = $lexRefs[2]->resolve();

        $this->assertInstanceOf(LexXrpcQuery::class, $entity1);
        $this->assertInstanceOf(LexObject::class, $entity2);
        $this->assertInstanceOf(LexToken::class, $entity3);
    }
}
