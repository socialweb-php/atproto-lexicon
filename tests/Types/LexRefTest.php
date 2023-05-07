<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use Mockery;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;
use SocialWeb\Atproto\Lexicon\Types\ParserFactoryRequired;
use SocialWeb\Atproto\Lexicon\Types\UnableToResolveReferences;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class LexRefTest extends TestCase
{
    public function testResolveThrowsExceptionWhenNoParserFactoryConfigured(): void
    {
        $lexRef = new LexRef();

        $this->expectException(ParserFactoryRequired::class);
        $this->expectExceptionMessage('You must provide a ParserFactory to the constructor to resolve references');

        $lexRef->resolve();
    }

    public function testResolveThrowsExceptionWhenEntityHasNoRef(): void
    {
        $parserFactory = $this->mockery(ParserFactory::class);
        $lexRef = new LexRef(parserFactory: $parserFactory);

        $this->expectException(UnableToResolveReferences::class);
        $this->expectExceptionMessage('Unable to resolve LexRef without a ref: {"type":"ref"}');

        $lexRef->resolve();
    }

    public function testResolveThrowsExceptionWhenRefIsNotValidNsid(): void
    {
        $parserFactory = $this->mockery(ParserFactory::class);
        $lexRef = new LexRef(ref: 'not-a-valid-nsid', parserFactory: $parserFactory);

        $this->expectException(UnableToResolveReferences::class);
        $this->expectExceptionMessage('Unable to resolve reference for invalid NSID: not-a-valid-nsid');

        $lexRef->resolve();
    }

    public function testResolveThrowsExceptionWhenUnableToLocateSchemaFile(): void
    {
        $parserFactory = $this->mockery(ParserFactory::class);
        $parserFactory
            ->expects('getSchemaRepository->findSchemaPathByNsid')
            ->with(Mockery::capture($nsid))
            ->andReturns(null);

        $lexRef = new LexRef(ref: 'com.example.foo#bar', parserFactory: $parserFactory);

        $this->expectException(UnableToResolveReferences::class);
        $this->expectExceptionMessage('Unable to locate schema file for ref: com.example.foo#bar');

        $lexRef->resolve();
    }

    public function testResolveThrowsExceptionWhenDefIdDoesNotExistInSchema(): void
    {
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new DefaultParserFactory($schemaRepository);

        $lexRef = new LexRef(ref: 'org.example.foo.getSomething#nothing', parserFactory: $parserFactory);

        $this->expectException(UnableToResolveReferences::class);
        $this->expectExceptionMessage(
            'Def ID "#nothing" does not exist in schema for NSID "org.example.foo.getSomething"',
        );

        $lexRef->resolve();
    }

    public function testResolveSucceeds(): void
    {
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new DefaultParserFactory($schemaRepository);

        $lexRef = new LexRef(ref: 'org.example.foo.getSomething', parserFactory: $parserFactory);
        $entity = $lexRef->resolve();

        $this->assertInstanceOf(LexXrpcQuery::class, $entity);
        $this->assertSame('Gets something really cool.', $entity->description);
        $this->assertInstanceOf(LexXrpcBody::class, $entity->output);
        $this->assertSame('application/json', $entity->output->encoding);
    }
}
