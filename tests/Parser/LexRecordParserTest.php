<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexRecordParser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRecord;
use SocialWeb\Atproto\Lexicon\Types\LexType;

class LexRecordParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexRecordParser::class;
    }

    /**
     * @param array<string, scalar | scalar[] | LexObject> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new SchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexRecordParser();
        $parser->setParserFactory(new ParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexRecord::class, $parsed);
        $this->assertSame(LexType::Record, $parsed->type);
        $this->assertSame($checkValues['key'] ?? null, $parsed->key);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // We use assertEquals() here, since we can't assert sameness on the object.
        $this->assertEquals($checkValues['record'], $parsed->record);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | LexObject>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON basic' => [
                'value' => '{"type":"record","record":{"type":"object"}}',
                'checkValues' => ['record' => new LexObject()],
            ],
            'object basic' => [
                'value' => (object) ['type' => 'record', 'record' => (object) ['type' => 'object']],
                'checkValues' => ['record' => new LexObject()],
            ],
            'JSON with description' => [
                'value' => '{"type":"record","description":"Hello there","record":{"type":"object"}}',
                'checkValues' => ['description' => 'Hello there', 'record' => new LexObject()],
            ],
            'object with description' => [
                'value' => (object) [
                    'type' => 'record', 'description' => 'Hello there', 'record' => (object) ['type' => 'object'],
                ],
                'checkValues' => ['description' => 'Hello there', 'record' => new LexObject()],
            ],
            'JSON with key' => [
                'value' => '{"type":"record","key":"foobar","record":{"type":"object"}}',
                'checkValues' => ['key' => 'foobar', 'record' => new LexObject()],
            ],
            'object with key' => [
                'value' => (object) ['type' => 'record', 'key' => 'foobar', 'record' => (object) ['type' => 'object']],
                'checkValues' => ['key' => 'foobar', 'record' => new LexObject()],
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
            ['value' => '{"type":"record"}'],
            ['value' => (object) ['type' => 'record']],
            ['value' => '{"type":"record","record":{"type":"object"},"description":true}'],
            ['value' =>
                (object) ['type' => 'record', 'record' => (object) ['type' => 'object'], 'description' => false],
            ],
            ['value' => '{"type":"record","record":{"type":"object"},"key":true}'],
            ['value' => (object) ['type' => 'record', 'record' => (object) ['type' => 'object'], 'key' => 123]],
        ];
    }
}
