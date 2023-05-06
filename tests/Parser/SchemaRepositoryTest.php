<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\TestWith;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\SchemaNotFound;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function realpath;

class SchemaRepositoryTest extends TestCase
{
    public function testWhenUnableToFindSchemaDirectory(): void
    {
        $this->expectException(SchemaNotFound::class);
        $this->expectExceptionMessage('Unable to find schema directory at "foo/bar/baz/"');

        new DefaultSchemaRepository('foo/bar/baz/');
    }

    public function testWhenArrayDoesNotContainOnlyStrings(): void
    {
        $this->expectException(SchemaNotFound::class);
        $this->expectExceptionMessage('All schema directory paths must be strings');

        /**
         * @psalm-suppress InvalidArgument
         * @phpstan-ignore-next-line
         */
        new DefaultSchemaRepository([__DIR__ . '/../schemas', 1234]);
    }

    #[TestWith([new Nsid('net.example.someOperation'), new LexiconDoc(id: new Nsid('net.example.someOperation'))])]
    #[TestWith([new Nsid('net.example.someOtherOperation'), null])]
    public function testFindSchemaByNsid(Nsid $nsid, ?LexiconDoc $expectedResult): void
    {
        $parsedSchemas = [];

        if ($expectedResult !== null) {
            $parsedSchemas[$expectedResult->id->nsid] = $expectedResult;
        }

        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas', $parsedSchemas);

        $this->assertSame($expectedResult, $schemaRepository->findSchemaByNsid($nsid));
    }

    public function testStoreSchema(): void
    {
        $schema = new LexiconDoc(id: new Nsid('net.example.someOperation'));
        $schemaRepository = new DefaultSchemaRepository(__DIR__ . '/../schemas');
        $nsid = new Nsid('net.example.someOperation');

        $this->assertNull($schemaRepository->findSchemaByNsid($nsid));

        $schemaRepository->storeSchema($schema);

        $this->assertSame($schema, $schemaRepository->findSchemaByNsid($nsid));
    }

    #[TestWith([
        new Nsid('org.example.foo.getSomething#main'),
        __DIR__ . '/../schemas/org/example/foo/getSomething.json',
    ])]
    #[TestWith([
        new Nsid('org.example.foo.getSomething'),
        __DIR__ . '/../schemas/org/example/foo/getSomething.json',
    ])]
    #[TestWith([new Nsid('net.example.notExists'), null])]
    #[TestWith([
        new Nsid('net.example.defs#inviteCodeUse'),
        __DIR__ . '/../more-schemas/net/example/defs.json',
    ])]
    public function testFindSchemaPathByNsid(Nsid $nsid, ?string $expectedPath): void
    {
        if ($expectedPath !== null) {
            $expectedPath = realpath($expectedPath);
        }

        $schemaRepository = new DefaultSchemaRepository([
            __DIR__ . '/../schemas',
            __DIR__ . '/../more-schemas',
        ]);

        $this->assertSame($expectedPath, $schemaRepository->findSchemaPathByNsid($nsid));
    }
}
