<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexObjectParser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexBoolean;
use SocialWeb\Atproto\Lexicon\Types\LexNumber;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;

class LexObjectParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexObjectParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new SchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexObjectParser();
        $parser->setParserFactory(new ParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexObject::class, $parsed);
        $this->assertSame(LexUserTypeType::Object, $parsed->type);
        $this->assertSame($checkValues['required'] ?? null, $parsed->required);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // We use assertEquals() here, since we can't assert sameness on the objects.
        $this->assertEquals($checkValues['properties'] ?? [], $parsed->properties);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | array<string, LexType>>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"object"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'object'],
                'checkValues' => [],
            ],
            'JSON with various types of properties' => [
                'value' => '{"type":"object","properties":{"aa":{"type":"array"},"bb":{"type":"blob"},'
                    . '"cc":{"type":"object"},"dd":{"type":"number"},"ee":{"type":"ref","ref":"io.foo.bar"},'
                    . '"ff":{"type":"union","refs":["io.baz.aaa", "io.baz.bbb"]},"gg":{"type":"unknown"}},'
                    . '"required":["aa","dd","gg"]}',
                'checkValues' => [
                    'properties' => [
                        'aa' => new LexArray(),
                        'bb' => new LexBlob(),
                        'cc' => new LexObject(),
                        'dd' => new LexNumber(),
                        'ee' => new LexRef('io.foo.bar'),
                        'ff' => new LexRefUnion(refs: ['io.baz.aaa', 'io.baz.bbb']),
                        'gg' => new LexUnknown(),
                    ],
                    'required' => ['aa', 'dd', 'gg'],
                ],
            ],
            'object with items as object' => [
                'value' => (object) [
                    'type' => 'object',
                    'required' => ['bb', 'ff'],
                    'properties' => (object) [
                        'aa' => (object) ['type' => 'array'],
                        'bb' => (object) ['type' => 'blob'],
                        'cc' => (object) ['type' => 'object'],
                        'dd' => (object) ['type' => 'boolean'],
                        'ee' => (object) ['type' => 'ref', 'ref' => 'com.example.fooBar#main'],
                        'ff' => (object) ['type' => 'union', 'refs' => ['io.foo.aaa', 'io.bar.bbb']],
                        'gg' => (object) ['type' => 'unknown'],
                    ],
                ],
                'checkValues' => [
                    'properties' => [
                        'aa' => new LexArray(),
                        'bb' => new LexBlob(),
                        'cc' => new LexObject(),
                        'dd' => new LexBoolean(),
                        'ee' => new LexRef('com.example.fooBar#main'),
                        'ff' => new LexRefUnion(refs: ['io.foo.aaa', 'io.bar.bbb']),
                        'gg' => new LexUnknown(),
                    ],
                    'required' => ['bb', 'ff'],
                ],
            ],
            'JSON with description' => [
                'value' => '{"type":"object","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'object', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
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
            ['value' => '{"type":"foo"}'],
            ['value' => (object) ['type' => 'foo']],
            ['value' => '{"type":"object","properties":{"foo":{"type":"number"},"bar":{"type":"token"}}}'],
            [
                'value' => (object) [
                    'type' => 'object',
                    'properties' => (object) [
                        'aa' => (object) ['type' => 'array'],
                        'bb' => (object) ['type' => 'blob'],
                        'cc' => (object) ['type' => 'object'],
                        'dd' => (object) ['type' => 'boolean'],
                        'ee' => (object) ['type' => 'ref', 'ref' => 'com.example.fooBar#main'],
                        'ff' => (object) ['type' => 'union', 'refs' => ['io.foo.aaa', 'io.bar.bbb']],
                        'gg' => (object) ['type' => 'unknown'],
                        'hh' => (object) ['type' => 'token'],
                    ],
                ],
            ],
            ['value' => '{"type":"object","description":false}'],
            ['value' => (object) ['type' => 'array', 'description' => false]],
        ];
    }
}
