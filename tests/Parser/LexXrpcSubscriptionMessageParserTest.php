<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcSubscriptionMessageParser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcSubscriptionMessage;

class LexXrpcSubscriptionMessageParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexXrpcSubscriptionMessageParser::class;
    }

    /**
     * @param array<string, scalar | scalar[] | LexObject | LexRef> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new SchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexXrpcSubscriptionMessageParser();
        $parser->setParserFactory(new ParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexXrpcSubscriptionMessage::class, $parsed);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // We use assertEquals() here, since we can't assert sameness on the object.
        $this->assertEquals($checkValues['schema'], $parsed->schema);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | LexObject | LexRef>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON basic' => [
                'value' => '{"schema":{"type":"object"}}',
                'checkValues' => ['schema' => new LexObject()],
            ],
            'object basic' => [
                'value' => (object) ['schema' => (object) ['type' => 'object']],
                'checkValues' => ['schema' => new LexObject()],
            ],
            'JSON with ref' => [
                'value' => '{"schema":{"type":"ref","ref":"com.example.foo"}}',
                'checkValues' => ['schema' => new LexRef(ref: 'com.example.foo')],
            ],
            'object with ref' => [
                'value' => (object) ['schema' => (object) ['type' => 'ref', 'ref' => 'com.example.bar']],
                'checkValues' => ['schema' => new LexRef(ref: 'com.example.bar')],
            ],
            'JSON with description' => [
                'value' => '{"schema":{"type":"object"},"description":"Hello there"}',
                'checkValues' => ['schema' => new LexObject(), 'description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['description' => 'Hello there', 'schema' => (object) ['type' => 'object']],
                'checkValues' => ['description' => 'Hello there', 'schema' => new LexObject()],
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
            ['value' => '{"schema":false}'],
            ['value' => (object) ['schema' => 'foobar']],
            ['value' => '{"schema":{"type":"string"}'],
            ['value' => (object) ['schema' => (object) ['type' => 'array']]],
            ['value' => '{"description":false}'],
            ['value' => (object) ['description' => 123]],
        ];
    }
}
