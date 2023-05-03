<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexiconDocParser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

use function json_encode;

class LexiconDocParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexiconDocParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new SchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexiconDocParser();
        $parser->setSchemaRepository($schemaRepo);
        $parser->setParserFactory(new ParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexiconDoc::class, $parsed);
        $this->assertSame(1, $parsed->lexicon);
        $this->assertSame($checkValues['id'] ?? null, $parsed->id);
        $this->assertSame($checkValues['revision'] ?? null, $parsed->revision);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
        $this->assertSame($checkValues['defs'] ?? '[]', json_encode($parsed->defs));
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON basic' => [
                'value' => '{"lexicon":1,"id":"com.example.foo"}',
                'checkValues' => ['id' => 'com.example.foo'],
            ],
            'object basic' => [
                'value' => (object) ['lexicon' => 1, 'id' => 'net.example.bar'],
                'checkValues' => ['id' => 'net.example.bar'],
            ],
            'JSON with revision as int' => [
                'value' => '{"lexicon":1,"id":"com.example.foo","revision":2}',
                'checkValues' => ['id' => 'com.example.foo', 'revision' => 2],
            ],
            'object with revision as int' => [
                'value' => (object) ['lexicon' => 1, 'id' => 'net.example.bar', 'revision' => 3],
                'checkValues' => ['id' => 'net.example.bar', 'revision' => 3],
            ],
            'JSON with revision as float' => [
                'value' => '{"lexicon":1,"id":"com.example.foo","revision":2.34}',
                'checkValues' => ['id' => 'com.example.foo', 'revision' => 2.34],
            ],
            'object with revision as float' => [
                'value' => (object) ['lexicon' => 1, 'id' => 'net.example.bar', 'revision' => 3.59],
                'checkValues' => ['id' => 'net.example.bar', 'revision' => 3.59],
            ],
            'JSON with description' => [
                'value' => '{"lexicon":1,"id":"com.example.foo","description":"A cool thing"}',
                'checkValues' => ['id' => 'com.example.foo', 'description' => 'A cool thing'],
            ],
            'object with description' => [
                'value' => (object) ['lexicon' => 1, 'id' => 'net.example.bar', 'description' => 'Another cool thing'],
                'checkValues' => ['id' => 'net.example.bar', 'description' => 'Another cool thing'],
            ],
            'JSON with defs' => [
                'value' => '{"lexicon":1,"id":"com.example.foo","defs":{"main":{"type":"string"},'
                    . '"foo":{"type":"integer"}}}',
                'checkValues' => [
                    'id' => 'com.example.foo',
                    // For easier testing, we convert this to JSON in testParsesValidValues().
                    'defs' => '{"main":{"type":"string","description":null,"format":null,"default":null,'
                        . '"minLength":null,"maxLength":null,"maxGraphemes":null,"enum":null,"const":null,'
                        . '"knownValues":null},"foo":{"type":"integer","description":null,"default":null,'
                        . '"minimum":null,"maximum":null,"enum":null,"const":null}}',
                ],
            ],
            'object with defs' => [
                'value' => (object) [
                    'lexicon' => 1, 'id' => 'net.example.bar', 'defs' => (object) [
                        'main' => (object) ['type' => 'number'],
                        'foo' => (object) ['type' => 'boolean'],
                    ],
                ],
                'checkValues' => [
                    'id' => 'net.example.bar',
                    // For easier testing, we convert this to JSON in testParsesValidValues().
                    'defs' => '{"main":{"type":"number","description":null,"default":null,"minimum":null,'
                        . '"maximum":null,"enum":null,"const":null},"foo":{"type":"boolean","description":null,'
                        . '"default":null,"const":null}}',
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
