<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexiconDocParser;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Atproto\Lexicon\Types\LexBoolean;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

use function file_get_contents;
use function json_encode;

class LexiconDocParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexiconDocParser::class;
    }

    public function testReturnsAlreadyParsedSchema(): void
    {
        $document = '{"lexicon":1,"id":"com.example.foo"}';

        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexiconDocParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));
        $parsed1 = $parser->parse($document);
        $parsed2 = $parser->parse($document);

        $this->assertSame($parsed1, $parsed2);
    }

    /**
     * @param array<string, scalar | scalar[] | Nsid | LexEntity[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexiconDocParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexiconDoc::class, $parsed);
        $this->assertSame(1, $parsed->lexicon);
        $this->assertSame($checkValues['revision'] ?? null, $parsed->revision);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // We use assertEquals() here, since we can't assert sameness on the object.
        $this->assertEquals($checkValues['id'] ?? null, $parsed->id);

        // Compare as JSON strings to avoid problems where the LexRef or LexUnion
        // objects in the parsed values fail equality checks due to the parser
        // factory instances they contain in private properties.
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['defs'] ?? []),
            (string) json_encode($parsed->defs),
        );

        foreach ($parsed->defs as $def) {
            $this->assertSame($parsed, $def->getParent());
        }
    }

    #[TestWith(['org.example.invalid.docWithNonMainProcedure'])]
    #[TestWith(['org.example.invalid.docWithNonMainQuery'])]
    #[TestWith(['org.example.invalid.docWithNonMainRecord'])]
    #[TestWith(['org.example.invalid.docWithNonMainSubscription'])]
    public function testThrowsWhenNonMainDefContainsImproperType(string $schemaNsid): void
    {
        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexiconDocParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));

        $nsid = new Nsid($schemaNsid);
        $schemaFile = (string) $schemaRepo->findSchemaPathByNsid($nsid);
        $schemaContents = (string) file_get_contents($schemaFile);

        $this->expectException(UnableToParse::class);
        $this->expectExceptionMessage('Records, procedures, queries, and subscriptions must be the main definition.');

        $parser->parse($schemaContents);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | Nsid | LexEntity[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON basic' => [
                'value' => '{"lexicon":1,"id":"com.example.foo"}',
                'checkValues' => ['id' => new Nsid('com.example.foo')],
            ],
            'object basic' => [
                'value' => (object) ['lexicon' => 1, 'id' => 'net.example.bar'],
                'checkValues' => ['id' => new Nsid('net.example.bar')],
            ],
            'JSON with revision as int' => [
                'value' => '{"lexicon":1,"id":"com.example.foo","revision":2}',
                'checkValues' => ['id' => new Nsid('com.example.foo'), 'revision' => 2],
            ],
            'object with revision as int' => [
                'value' => (object) ['lexicon' => 1, 'id' => 'net.example.bar', 'revision' => 3],
                'checkValues' => ['id' => new Nsid('net.example.bar'), 'revision' => 3],
            ],
            'JSON with revision as float' => [
                'value' => '{"lexicon":1,"id":"com.example.foo","revision":2.34}',
                'checkValues' => ['id' => new Nsid('com.example.foo'), 'revision' => 2.34],
            ],
            'object with revision as float' => [
                'value' => (object) ['lexicon' => 1, 'id' => 'net.example.bar', 'revision' => 3.59],
                'checkValues' => ['id' => new Nsid('net.example.bar'), 'revision' => 3.59],
            ],
            'JSON with description' => [
                'value' => '{"lexicon":1,"id":"com.example.foo","description":"A cool thing"}',
                'checkValues' => ['id' => new Nsid('com.example.foo'), 'description' => 'A cool thing'],
            ],
            'object with description' => [
                'value' => (object) ['lexicon' => 1, 'id' => 'net.example.bar', 'description' => 'Another cool thing'],
                'checkValues' => ['id' => new Nsid('net.example.bar'), 'description' => 'Another cool thing'],
            ],
            'JSON with defs' => [
                'value' => '{"lexicon":1,"id":"com.example.foo","defs":{"main":{"type":"string"},'
                    . '"foo":{"type":"integer"}}}',
                'checkValues' => [
                    'id' => new Nsid('com.example.foo'),
                    'defs' => ['main' => new LexString(), 'foo' => new LexInteger()],
                ],
            ],
            'object with defs' => [
                'value' => (object) [
                    'lexicon' => 1, 'id' => 'net.example.bar', 'defs' => (object) [
                        'main' => (object) ['type' => 'integer'],
                        'foo' => (object) ['type' => 'boolean'],
                    ],
                ],
                'checkValues' => [
                    'id' => new Nsid('net.example.bar'),
                    'defs' => ['main' => new LexInteger(), 'foo' => new LexBoolean()],
                ],
            ],
        ];
    }

    /**
     * @return array<array{value: object | string}>
     */
    public static function invalidValuesProvider(): array
    {
        return [
            ['value' => ''],
            ['value' => '{}'],
            ['value' => (object) []],
            ['value' => '{"lexicon":2,"id":"com.example.foobar"}'],
            ['value' => (object) ['lexicon' => 2, 'id' => 'com.example.foobar']],
            ['value' => '{"lexicon":1,"id":"invalid_nsid"}'],
            ['value' => (object) ['lexicon' => 1, 'id' => 'not_valid']],
            ['value' => '{"lexicon":1,"id":"com.example.foobar","revision":"1234"}'],
            ['value' => (object) ['lexicon' => 1, 'id' => 'com.example.foobar', 'revision' => false]],
            ['value' => '{"lexicon":1,"id":"com.example.foobar","description":1234}'],
            ['value' => (object) ['lexicon' => 1, 'id' => 'com.example.foobar', 'description' => false]],
            ['value' => '{"lexicon":1,"id":"com.example.foobar","defs":false}'],
            ['value' => (object) ['lexicon' => 1, 'id' => 'com.example.foobar', 'defs' => [123]]],
        ];
    }
}
