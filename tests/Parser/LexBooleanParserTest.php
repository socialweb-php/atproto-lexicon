<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexBooleanParser;
use SocialWeb\Atproto\Lexicon\Types\LexBoolean;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveType;

class LexBooleanParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexBooleanParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexBooleanParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexBoolean::class, $parsed);
        $this->assertSame(LexPrimitiveType::Boolean, $parsed->type);
        $this->assertSame($checkValues['default'] ?? null, $parsed->default);
        $this->assertSame($checkValues['const'] ?? null, $parsed->const);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"boolean"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'boolean'],
                'checkValues' => [],
            ],
            'JSON with default' => [
                'value' => '{"type":"boolean","default":true}',
                'checkValues' => ['default' => true],
            ],
            'object with default' => [
                'value' => (object) ['type' => 'boolean', 'default' => false],
                'checkValues' => ['default' => false],
            ],
            'JSON with const' => [
                'value' => '{"type":"boolean","const":true}',
                'checkValues' => ['const' => true],
            ],
            'object with const' => [
                'value' => (object) ['type' => 'boolean', 'const' => false],
                'checkValues' => ['const' => false],
            ],
            'JSON with description' => [
                'value' => '{"type":"boolean","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'boolean', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'JSON with all properties' => [
                'value' => '{"type":"boolean","default":true,"const":false,"description":"Hello there"}',
                'checkValues' => ['default' => true, 'const' => false, 'description' => 'Hello there'],
            ],
            'object with all properties' => [
                'value' => (object) [
                    'type' => 'boolean', 'default' => false, 'const' => true, 'description' => 'Hello there',
                ],
                'checkValues' => ['default' => false, 'const' => true, 'description' => 'Hello there'],
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
            ['value' => '{"type":"boolean","default":123}'],
            ['value' => (object) ['type' => 'boolean', 'default' => 123]],
            ['value' => '{"type":"boolean","default":true,"const":456}'],
            ['value' => (object) ['type' => 'boolean', 'default' => true, 'const' => 456]],
            ['value' => '{"type":"boolean","default":true,"const":false,"description":true}'],
            ['value' => (object) ['type' => 'boolean', 'default' => true, 'const' => false, 'description' => true]],
        ];
    }
}
