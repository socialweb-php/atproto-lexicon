<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexObjectParser;
use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexBoolean;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;

use function json_encode;

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
        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexObjectParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexObject::class, $parsed);
        $this->assertSame(LexType::Object, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
        $this->assertSame($checkValues['required'] ?? null, $parsed->required);
        $this->assertSame($checkValues['nullable'] ?? null, $parsed->nullable);

        // Compare as JSON strings to avoid problems where the LexRef or LexUnion
        // objects in the parsed values fail equality checks due to the parser
        // factory instances they contain in private properties.
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['properties'] ?? []),
            (string) json_encode($parsed->properties),
        );
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | array<string, LexEntity>>}>
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
                    . '"cc":{"type":"string"},"dd":{"type":"integer"},"ee":{"type":"ref","ref":"io.foo.bar"},'
                    . '"ff":{"type":"union","refs":["io.baz.aaa", "io.baz.bbb"]},"gg":{"type":"unknown"}},'
                    . '"required":["aa","dd","gg"],"nullable":["dd","gg"]}',
                'checkValues' => [
                    'properties' => [
                        'aa' => new LexArray(),
                        'bb' => new LexBlob(),
                        'cc' => new LexString(),
                        'dd' => new LexInteger(),
                        'ee' => new LexRef(ref: 'io.foo.bar'),
                        'ff' => new LexRefUnion(refs: ['io.baz.aaa', 'io.baz.bbb']),
                        'gg' => new LexUnknown(),
                    ],
                    'required' => ['aa', 'dd', 'gg'],
                    'nullable' => ['dd', 'gg'],
                ],
            ],
            'object with items as object' => [
                'value' => (object) [
                    'type' => 'object',
                    'required' => ['bb', 'ff'],
                    'nullable' => ['ff'],
                    'properties' => (object) [
                        'aa' => (object) ['type' => 'array'],
                        'bb' => (object) ['type' => 'blob'],
                        'cc' => (object) ['type' => 'string'],
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
                        'cc' => new LexString(),
                        'dd' => new LexBoolean(),
                        'ee' => new LexRef(ref: 'com.example.fooBar#main'),
                        'ff' => new LexRefUnion(refs: ['io.foo.aaa', 'io.bar.bbb']),
                        'gg' => new LexUnknown(),
                    ],
                    'required' => ['bb', 'ff'],
                    'nullable' => ['ff'],
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
            ['value' => '{"type":"object","properties":{"foo":{"type":"integer"},"bar":{"type":"token"}}}'],
            [
                'value' => (object) [
                    'type' => 'object',
                    'properties' => (object) [
                        'aa' => (object) ['type' => 'array'],
                        'bb' => (object) ['type' => 'blob'],
                        'cc' => (object) ['type' => 'string'],
                        'dd' => (object) ['type' => 'boolean'],
                        'ee' => (object) ['type' => 'ref', 'ref' => 'com.example.fooBar#main'],
                        'ff' => (object) ['type' => 'union', 'refs' => ['io.foo.aaa', 'io.bar.bbb']],
                        'gg' => (object) ['type' => 'unknown'],
                        'hh' => (object) ['type' => 'token'],
                    ],
                ],
            ],
            ['value' => '{"type":"object","required":["foo",1]}'],
            ['value' => (object) ['type' => 'object', 'required' => 'foobar']],
            ['value' => '{"type":"object","nullable":[23.1,"bar"]}'],
            ['value' => (object) ['type' => 'object', 'nullable' => false]],
            ['value' => '{"type":"object","description":false}'],
            ['value' => (object) ['type' => 'object', 'description' => false]],
        ];
    }
}
