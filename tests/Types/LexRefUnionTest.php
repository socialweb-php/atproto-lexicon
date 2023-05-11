<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;
use SocialWeb\Atproto\Lexicon\Types\LexResolvable;
use SocialWeb\Atproto\Lexicon\Types\LexToken;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class LexRefUnionTest extends TestCase
{
    public function testResolvesRefs(): void
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

        $this->assertInstanceOf(LexResolvable::class, $lexRefUnion);

        $lexCollection = $lexRefUnion->resolve();

        $this->assertCount(3, $lexCollection);
        $this->assertInstanceOf(LexXrpcQuery::class, $lexCollection[0]);
        $this->assertInstanceOf(LexObject::class, $lexCollection[1]);
        $this->assertInstanceOf(LexToken::class, $lexCollection[2]);
    }
}
