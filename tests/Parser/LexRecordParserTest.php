<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexRecordParser;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRecord;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function json_encode;

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
        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexRecordParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexRecord::class, $parsed);
        $this->assertSame(LexType::Record, $parsed->type);
        $this->assertSame($checkValues['key'] ?? null, $parsed->key);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // Compare as JSON strings to avoid problems where objects in the parsed
        // values fail equality checks due to the parser factory instances they
        // contain in private properties.
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['record'] ?? null),
            (string) json_encode($parsed->record),
        );

        if ($parsed->record !== null) {
            $this->assertSame($parsed, $parsed->record->getParent());
        }
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
