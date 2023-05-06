<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcQueryParser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcParameters;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;

class LexXrpcQueryParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexXrpcQueryParser::class;
    }

    /**
     * @param array<string, scalar | scalar[] | LexXrpcBody | array<string | LexPrimitive> | LexXrpcError[] | LexXrpcParameters> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new SchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexXrpcQueryParser();
        $parser->setParserFactory(new ParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexXrpcQuery::class, $parsed);
        $this->assertSame(LexType::Query, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // We use assertEquals() here, since we can't assert sameness on the objects.
        $this->assertEquals($checkValues['parameters'] ?? null, $parsed->parameters);
        $this->assertEquals($checkValues['errors'] ?? null, $parsed->errors);
        $this->assertEquals($checkValues['output'] ?? null, $parsed->output);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | LexXrpcBody | array<string | LexPrimitive> | LexXrpcError[] | LexXrpcParameters>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"query"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'query'],
                'checkValues' => [],
            ],
            'JSON with parameters' => [
                'value' => '{"type":"query","parameters":{"type":"params","properties":{"foo":{"type":"string"},'
                    . '"bar":{"type":"integer"}}}}',
                'checkValues' => [
                    'parameters' => new LexXrpcParameters(
                        properties: ['foo' => new LexString(), 'bar' => new LexInteger()],
                    ),
                ],
            ],
            'object with parameters' => [
                'value' => (object) [
                    'type' => 'query',
                    'parameters' => (object) [
                        'type' => 'params',
                        'properties' => (object) ['baz' => (object) ['type' => 'integer']],
                    ],
                ],
                'checkValues' => ['parameters' => new LexXrpcParameters(properties: ['baz' => new LexInteger()])],
            ],
            'JSON with errors' => [
                'value' => '{"type":"query","errors":[{"name":"MyError"},{"name":"YourError"}]}',
                'checkValues' => ['errors' => [new LexXrpcError('MyError'), new LexXrpcError('YourError')]],
            ],
            'object with errors' => [
                'value' => (object) ['type' => 'query', 'errors' => [(object) ['name' => 'AnError']]],
                'checkValues' => ['errors' => [new LexXrpcError('AnError')]],
            ],
            'JSON with output' => [
                'value' => '{"type":"query","output":{"encoding":"application/json","schema":{"type":"object"}}}',
                'checkValues' => ['output' => new LexXrpcBody(encoding: 'application/json', schema: new LexObject())],
            ],
            'object with output' => [
                'value' => (object) [
                    'type' => 'query',
                    'output' => (object) ['encoding' => 'application/xml', 'schema' => (object) ['type' => 'object']],
                ],
                'checkValues' => ['output' => new LexXrpcBody(encoding: 'application/xml', schema: new LexObject())],
            ],
            'JSON with description' => [
                'value' => '{"type":"query","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'query', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'query type should ignore input' => [
                'value' => (object) ['type' => 'query', 'input' => 'invalid input'],
                'checkValues' => [],
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
            ['value' => '{"type":"query","parameters":false}'],
            ['value' => (object) ['type' => 'query', 'parameters' => 'foobar']],
            ['value' => '{"type":"query","errors":123}'],
            ['value' => (object) ['type' => 'query', 'errors' => 'foobar']],
            ['value' => (object) ['type' => 'query', 'output' => true]],
            ['value' => '{"type":"query","description":false}'],
            ['value' => (object) ['type' => 'query', 'description' => false]],
        ];
    }
}
