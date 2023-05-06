<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcProcedureParser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;

class LexXrpcProcedureParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexXrpcProcedureParser::class;
    }

    /**
     * @param array<string, scalar | scalar[] | LexXrpcBody | array<string | LexPrimitive> | LexXrpcError[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new SchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexXrpcProcedureParser();
        $parser->setParserFactory(new ParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexXrpcProcedure::class, $parsed);
        $this->assertSame(LexUserTypeType::Procedure, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // We use assertEquals() here, since we can't assert sameness on the objects.
        $this->assertEquals($checkValues['parameters'] ?? null, $parsed->parameters);
        $this->assertEquals($checkValues['errors'] ?? null, $parsed->errors);
        $this->assertEquals($checkValues['input'] ?? null, $parsed->input);
        $this->assertEquals($checkValues['output'] ?? null, $parsed->output);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | LexXrpcBody | array<string | LexPrimitive> | LexXrpcError[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"procedure"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'procedure'],
                'checkValues' => [],
            ],
            'JSON with parameters' => [
                'value' => '{"type":"procedure","parameters":{"foo":{"type":"string"},"bar":{"type":"integer"}}}',
                'checkValues' => ['parameters' => ['foo' => new LexString(), 'bar' => new LexInteger()]],
            ],
            'object with parameters' => [
                'value' => (object) [
                    'type' => 'procedure', 'parameters' => (object) ['baz' => (object) ['type' => 'integer']],
                ],
                'checkValues' => ['parameters' => ['baz' => new LexInteger()]],
            ],
            'JSON with errors' => [
                'value' => '{"type":"procedure","errors":[{"name":"MyError"},{"name":"YourError"}]}',
                'checkValues' => ['errors' => [new LexXrpcError('MyError'), new LexXrpcError('YourError')]],
            ],
            'object with errors' => [
                'value' => (object) ['type' => 'procedure', 'errors' => [(object) ['name' => 'AnError']]],
                'checkValues' => ['errors' => [new LexXrpcError('AnError')]],
            ],
            'JSON with input' => [
                'value' => '{"type":"procedure","input":{"encoding":"text/plain","schema":{"type":"object"}}}',
                'checkValues' => ['input' => new LexXrpcBody(encoding: 'text/plain', schema: new LexObject())],
            ],
            'object with input' => [
                'value' => (object) [
                    'type' => 'procedure',
                    'input' => (object) ['encoding' => 'text/html', 'schema' => (object) ['type' => 'object']],
                ],
                'checkValues' => ['input' => new LexXrpcBody(encoding: 'text/html', schema: new LexObject())],
            ],
            'JSON with output' => [
                'value' => '{"type":"procedure","output":{"encoding":"application/json","schema":{"type":"object"}}}',
                'checkValues' => ['output' => new LexXrpcBody(encoding: 'application/json', schema: new LexObject())],
            ],
            'object with output' => [
                'value' => (object) [
                    'type' => 'procedure',
                    'output' => (object) ['encoding' => 'application/xml', 'schema' => (object) ['type' => 'object']],
                ],
                'checkValues' => ['output' => new LexXrpcBody(encoding: 'application/xml', schema: new LexObject())],
            ],
            'JSON with description' => [
                'value' => '{"type":"procedure","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'procedure', 'description' => 'Hello there'],
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
            ['value' => '{"type":"procedure","parameters":false}'],
            ['value' =>
                (object) ['type' => 'procedure', 'parameters' => (object) ['foo' => (object) ['type' => 'object']]],
            ],
            ['value' => '{"type":"procedure","errors":123}'],
            ['value' => (object) ['type' => 'procedure', 'errors' => 'foobar']],
            ['value' => '{"type":"procedure","input":"hello"}'],
            ['value' => (object) ['type' => 'procedure', 'input' => false]],
            ['value' => '{"type":"procedure","output":19.56}'],
            ['value' => (object) ['type' => 'procedure', 'output' => true]],
            ['value' => '{"type":"procedure","description":false}'],
            ['value' => (object) ['type' => 'procedure', 'description' => false]],
        ];
    }
}
