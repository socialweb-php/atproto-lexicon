<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcQueryParser;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcParameters;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;

use function json_encode;

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
        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexXrpcQueryParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexXrpcQuery::class, $parsed);
        $this->assertSame(LexType::Query, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // Compare as JSON strings to avoid problems where the LexRef or LexUnion
        // objects in the parsed values fail equality checks due to the parser
        // factory instances they contain in private properties.
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['parameters'] ?? null),
            (string) json_encode($parsed->parameters),
        );
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['errors'] ?? null),
            (string) json_encode($parsed->errors),
        );
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['output'] ?? null),
            (string) json_encode($parsed->output),
        );

        if ($parsed->parameters !== null) {
            $this->assertSame($parsed, $parsed->parameters->getParent());
        }

        foreach ($parsed->errors ?? [] as $error) {
            $this->assertSame($parsed, $error->getParent());
        }

        if ($parsed->output !== null) {
            $this->assertSame($parsed, $parsed->output->getParent());
        }
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
