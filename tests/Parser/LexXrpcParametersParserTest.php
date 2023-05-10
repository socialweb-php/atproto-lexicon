<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcParametersParser;
use SocialWeb\Atproto\Lexicon\Types\LexBoolean;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveArray;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcParameters;

use function json_encode;

class LexXrpcParametersParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexXrpcParametersParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexXrpcParametersParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexXrpcParameters::class, $parsed);
        $this->assertSame(LexType::Params, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
        $this->assertSame($checkValues['required'] ?? null, $parsed->required);

        // Compare as JSON strings to avoid problems where the LexRef or LexUnion
        // objects in the parsed values fail equality checks due to the parser
        // factory instances they contain in private properties.
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['properties'] ?? []),
            (string) json_encode($parsed->properties),
        );

        foreach ($parsed->properties as $property) {
            $this->assertSame($parsed, $property->getParent());
        }
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | array<string, LexEntity>>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"params"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'params'],
                'checkValues' => [],
            ],
            'JSON with various types of properties' => [
                'value' => '{"type":"params","properties":{"aa":{"type":"array","items":{"type":"string"}},'
                    . '"bb":{"type":"boolean"},"cc":{"type":"string"},"dd":{"type":"integer"},"gg":{"type":"unknown"}},'
                    . '"required":["aa","dd","gg"]}',
                'checkValues' => [
                    'properties' => [
                        'aa' => new LexPrimitiveArray(items: new LexString()),
                        'bb' => new LexBoolean(),
                        'cc' => new LexString(),
                        'dd' => new LexInteger(),
                        'gg' => new LexUnknown(),
                    ],
                    'required' => ['aa', 'dd', 'gg'],
                    'nullable' => ['dd', 'gg'],
                ],
            ],
            'object with items as object' => [
                'value' => (object) [
                    'type' => 'params',
                    'required' => ['bb', 'ff'],
                    'properties' => (object) [
                        'aa' => (object) ['type' => 'array', 'items' => (object) ['type' => 'integer']],
                        'bb' => (object) ['type' => 'boolean'],
                        'cc' => (object) ['type' => 'string'],
                        'dd' => (object) ['type' => 'integer'],
                        'gg' => (object) ['type' => 'unknown'],
                    ],
                ],
                'checkValues' => [
                    'properties' => [
                        'aa' => new LexPrimitiveArray(items: new LexInteger()),
                        'bb' => new LexBoolean(),
                        'cc' => new LexString(),
                        'dd' => new LexInteger(),
                        'gg' => new LexUnknown(),
                    ],
                    'required' => ['bb', 'ff'],
                ],
            ],
            'JSON with description' => [
                'value' => '{"type":"params","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'params', 'description' => 'Hello there'],
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
            ['value' => '{"type":"params","properties":{"foo":{"type":"integer"},"bar":{"type":"array"}}}'],
            [
                'value' => (object) [
                    'type' => 'params',
                    'properties' => (object) [
                        'aa' => (object) ['type' => 'array', 'items' => (object) ['type' => 'unknown']],
                        'bb' => (object) ['type' => 'integer'],
                        'cc' => (object) ['type' => 'string'],
                        'dd' => (object) ['type' => 'boolean'],
                        'gg' => (object) ['type' => 'unknown'],
                        'hh' => (object) ['type' => 'array', 'items' => (object) ['type' => 'blob']],
                    ],
                ],
            ],
            ['value' => '{"type":"params","required":["foo",1]}'],
            ['value' => (object) ['type' => 'params', 'required' => 'foobar']],
            ['value' => '{"type":"params","description":false}'],
            ['value' => (object) ['type' => 'params', 'description' => false]],
        ];
    }
}
