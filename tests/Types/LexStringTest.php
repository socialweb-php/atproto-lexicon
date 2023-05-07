<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexToken;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class LexStringTest extends TestCase
{
    public function testGetLexRefsForKnownValues(): void
    {
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new DefaultParserFactory($schemaRepository);

        $lexRef = new LexRef(
            ref: 'org.example.foo.getSomething#reasonType',
            parserFactory: $parserFactory,
        );

        $lexString = $lexRef->resolve();
        $this->assertInstanceOf(LexString::class, $lexString);

        $lexRefs = $lexString->getLexRefsForKnownValues();

        $this->assertCount(6, $lexRefs);
        $this->assertContainsOnlyInstancesOf(LexRef::class, $lexRefs);

        foreach ($lexRefs as $lexRef) {
            $this->assertInstanceOf(LexToken::class, $lexRef->resolve());
        }
    }
}
