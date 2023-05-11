<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use Mockery;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexResolvable;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;
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
        $this->assertInstanceOf(LexResolvable::class, $lexRef);

        $entity = $lexRef->resolve();

        $this->assertInstanceOf(LexXrpcQuery::class, $entity);
        $this->assertSame('Gets something really cool.', $entity->description);
        $this->assertInstanceOf(LexXrpcBody::class, $entity->output);
        $this->assertSame('application/json', $entity->output->encoding);

        $entityParent = $entity->getParent();
        $this->assertInstanceOf(LexiconDoc::class, $entityParent);
        $this->assertSame('org.example.foo.getSomething', $entityParent->id->nsid);
    }

    public function testResolveWithGivenParent(): void
    {
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new DefaultParserFactory($schemaRepository);

        $lexiconDoc = new LexiconDoc(new Nsid('org.example.foo.getSomething'));

        $lexRef = new LexRef(ref: '#actionReversal', parserFactory: $parserFactory);
        $lexRef->setParent($lexiconDoc);

        $entity = $lexRef->resolve();

        $this->assertInstanceOf(LexObject::class, $entity);
        $this->assertCount(3, $entity->properties);

        $entityParent = $entity->getParent();
        $this->assertInstanceOf(LexiconDoc::class, $entityParent);
        $this->assertSame('org.example.foo.getSomething', $entityParent->id->nsid);
    }

    public function testResolveRelativeSchema(): void
    {
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new DefaultParserFactory($schemaRepository);

        $lexRef = new LexRef(ref: 'org.example.foo.getSomething', parserFactory: $parserFactory);
        $entity = $lexRef->resolve();

        $this->assertInstanceOf(LexXrpcQuery::class, $entity);
        $this->assertInstanceOf(LexXrpcBody::class, $entity->output);
        $this->assertInstanceOf(LexObject::class, $entity->output->schema);

        $codeRef = $entity->output->schema->properties['code'];
        $this->assertInstanceOf(LexRef::class, $codeRef);

        $codeProperty = $codeRef->resolve();

        $this->assertInstanceOf(LexObject::class, $codeProperty);
        $this->assertArrayHasKey('uses', $codeProperty->properties);
        $this->assertInstanceOf(LexArray::class, $codeProperty->properties['uses']);
        $this->assertInstanceOf(LexRef::class, $codeProperty->properties['uses']->items);
        $this->assertSame('#inviteCodeUse', $codeProperty->properties['uses']->items->ref);

        $usesProperty = $codeProperty->properties['uses']->items->resolve();

        $this->assertInstanceOf(LexObject::class, $usesProperty);
        $this->assertArrayHasKey('usedBy', $usesProperty->properties);
        $this->assertInstanceOf(LexString::class, $usesProperty->properties['usedBy']);
    }

    public function testThrowsExceptionForUnresolvableRelativeReference(): void
    {
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new DefaultParserFactory($schemaRepository);

        $lexRef = new LexRef(ref: '#unresolvableReference', parserFactory: $parserFactory);

        $this->expectException(UnableToResolveReferences::class);
        $this->expectExceptionMessage('Unable to resolve relative reference: #unresolvableReference');

        $lexRef->resolve();
    }
}
