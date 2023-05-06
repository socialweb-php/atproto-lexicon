<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcBodyParser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;

class LexXrpcBodyParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexXrpcBodyParser::class;
    }

    /**
     * @param array<string, scalar | scalar[] | LexObject> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new SchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexXrpcBodyParser();
        $parser->setParserFactory(new ParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexXrpcBody::class, $parsed);
        $this->assertSame($checkValues['encoding'], $parsed->encoding);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // We use assertEquals() here, since we can't assert sameness on the object.
        $this->assertEquals($checkValues['schema'], $parsed->schema);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | LexEntity>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON basic' => [
                'value' => '{"encoding":"application/json","schema":{"type":"object"}}',
                'checkValues' => ['encoding' => 'application/json', 'schema' => new LexObject()],
            ],
            'object basic' => [
                'value' => (object) ['encoding' => 'application/json', 'schema' => (object) ['type' => 'object']],
                'checkValues' => ['encoding' => 'application/json', 'schema' => new LexObject()],
            ],
            'JSON with ref' => [
                'value' => '{"encoding":"application/json","schema":{"type":"ref","ref":"com.example.foo"}}',
                'checkValues' => ['encoding' => 'application/json', 'schema' => new LexRef(ref: 'com.example.foo')],
            ],
            'object with ref' => [
                'value' => (object) [
                    'encoding' => 'application/json',
                    'schema' => (object) ['type' => 'ref', 'ref' => 'com.example.bar'],
                ],
                'checkValues' => ['encoding' => 'application/json', 'schema' => new LexRef(ref: 'com.example.bar')],
            ],
            'JSON with description' => [
                'value' => '{"encoding":"text/plain","schema":{"type":"object"},'
                    . '"type":"record","description":"Hello there"}',
                'checkValues' => [
                    'encoding' => 'text/plain', 'schema' => new LexObject(), 'description' => 'Hello there',
                ],
            ],
            'object with description' => [
                'value' => (object) [
                    'encoding' => 'text/html',
                    'description' => 'Hello there',
                    'schema' => (object) ['type' => 'object'],
                ],
                'checkValues' => [
                    'encoding' => 'text/html', 'description' => 'Hello there', 'schema' => new LexObject(),
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
            ['value' => '{"encoding":123}'],
            ['value' => (object) ['encoding' => false]],
            ['value' => '{"encoding":"text/plain","schema":false}'],
            ['value' => (object) ['encoding' => 'text/plain', 'schema' => 'foobar']],
            ['value' => '{"encoding":"text/plain","schema":{"type":"object"}},"description":false'],
            ['value' =>
                (object) ['encoding' => 'text/plain', 'schema' => (object) ['type' => 'object'], 'description' => 123],
            ],
        ];
    }
}
