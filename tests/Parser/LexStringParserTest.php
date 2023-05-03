<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexStringParser;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveType;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function is_string;
use function json_encode;

use const JSON_UNESCAPED_SLASHES;

class LexStringParserTest extends TestCase
{
    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexStringParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexString::class, $parsed);
        $this->assertSame(LexPrimitiveType::String, $parsed->type);
        $this->assertSame($checkValues['format'] ?? null, $parsed->format);
        $this->assertSame($checkValues['default'] ?? null, $parsed->default);
        $this->assertSame($checkValues['minLength'] ?? null, $parsed->minLength);
        $this->assertSame($checkValues['maxLength'] ?? null, $parsed->maxLength);
        $this->assertSame($checkValues['maxGraphemes'] ?? null, $parsed->maxGraphemes);
        $this->assertSame($checkValues['enum'] ?? null, $parsed->enum);
        $this->assertSame($checkValues['const'] ?? null, $parsed->const);
        $this->assertSame($checkValues['knownValues'] ?? null, $parsed->knownValues);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    #[DataProvider('invalidValuesProvider')]
    public function testThrowsForInvalidValues(object | string $value): void
    {
        $parser = new LexStringParser();

        $this->expectException(UnableToParse::class);
        $this->expectExceptionMessage(
            'The input data does not contain a valid schema definition: "'
            . (is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_SLASHES)) . '"',
        );

        $parser->parse($value);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"string"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'string'],
                'checkValues' => [],
            ],
            'JSON with format as string' => [
                'value' => '{"type":"string","format":"datetime"}',
                'checkValues' => ['format' => 'datetime'],
            ],
            'JSON with default as string' => [
                'value' => '{"type":"string","default":"foo"}',
                'checkValues' => ['default' => 'foo'],
            ],
            'JSON with minLength as int' => [
                'value' => '{"type":"string","minLength":3}',
                'checkValues' => ['minLength' => 3],
            ],
            'object with minLength as int' => [
                'value' => (object) ['type' => 'string', 'minLength' => 3],
                'checkValues' => ['minLength' => 3],
            ],
            'JSON with maxLength as int' => [
                'value' => '{"type":"string","maxLength":1001}',
                'checkValues' => ['maxLength' => 1001],
            ],
            'object with maxLength as int' => [
                'value' => (object) ['type' => 'string', 'maxLength' => 1002],
                'checkValues' => ['maxLength' => 1002],
            ],
            'JSON with maxGraphemes as int' => [
                'value' => '{"type":"string","maxGraphemes":10219}',
                'checkValues' => ['maxGraphemes' => 10219],
            ],
            'object with maxGraphemes as int' => [
                'value' => (object) ['type' => 'string', 'maxGraphemes' => 82],
                'checkValues' => ['maxGraphemes' => 82],
            ],
            'JSON with enum as string[]' => [
                'value' => '{"type":"string","enum":["foo","bar","baz"]}',
                'checkValues' => ['enum' => ['foo', 'bar', 'baz']],
            ],
            'object with enum as string[]' => [
                'value' => (object) ['type' => 'string', 'enum' => ['foo', 'bar', 'baz']],
                'checkValues' => ['enum' => ['foo', 'bar', 'baz']],
            ],
            'JSON with const as string' => [
                'value' => '{"type":"string","const":"59"}',
                'checkValues' => ['const' => '59'],
            ],
            'object with const as string' => [
                'value' => (object) ['type' => 'string', 'const' => 'foo'],
                'checkValues' => ['const' => 'foo'],
            ],
            'JSON with knownValues as string[]' => [
                'value' => '{"type":"string","knownValues":["foo","bar","baz"]}',
                'checkValues' => ['knownValues' => ['foo', 'bar', 'baz']],
            ],
            'object with knownValues as string[]' => [
                'value' => (object) ['type' => 'string', 'knownValues' => ['foo', 'bar', 'baz']],
                'checkValues' => ['knownValues' => ['foo', 'bar', 'baz']],
            ],
            'JSON with description' => [
                'value' => '{"type":"string","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'string', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'JSON with all values' => [
                'value' => '{"type":"string","format":"uri","default":"something","minLength":0,"maxLength":255,'
                    . '"maxGraphemes":235,"enum":["what","is","this"],"const":"is","knownValues":["well","ha"],'
                    . '"description":"Well then"}',
                'checkValues' => [
                    'format' => 'uri',
                    'default' => 'something',
                    'minLength' => 0,
                    'maxLength' => 255,
                    'maxGraphemes' => 235,
                    'enum' => ['what', 'is', 'this'],
                    'const' => 'is',
                    'knownValues' => ['well', 'ha'],
                    'description' => 'Well then',
                ],
            ],
            'object with all values' => [
                'value' => (object) [
                    'type' => 'string',
                    'format' => 'uri',
                    'default' => 'something',
                    'minLength' => 0,
                    'maxLength' => 255,
                    'maxGraphemes' => 235,
                    'enum' => ['what', 'is', 'this'],
                    'const' => 'is',
                    'knownValues' => ['well', 'ha'],
                    'description' => 'Well then',
                ],
                'checkValues' => [
                    'format' => 'uri',
                    'default' => 'something',
                    'minLength' => 0,
                    'maxLength' => 255,
                    'maxGraphemes' => 235,
                    'enum' => ['what', 'is', 'this'],
                    'const' => 'is',
                    'knownValues' => ['well', 'ha'],
                    'description' => 'Well then',
                ],
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
            ['value' => '{"type":"string","format":123}'],
            ['value' => (object) ['type' => 'string', 'format' => 123]],
            ['value' => '{"type":"string","default":42.1}'],
            ['value' => (object) ['type' => 'string', 'default' => 42.1]],
            ['value' => '{"type":"string","minLength":"abc"}'],
            ['value' => (object) ['type' => 'string', 'minLength' => 56.2]],
            ['value' => '{"type":"string","maxLength":"def"}'],
            ['value' => (object) ['type' => 'string', 'maxLength' => 47.1]],
            ['value' => '{"type":"string","maxGraphemes":"ghi"}'],
            ['value' => (object) ['type' => 'string', 'maxGraphemes' => 81.4]],
            ['value' => '{"type":"string","enum":[2,3,"4"]}'],
            ['value' => (object) ['type' => 'string', 'enum' => [1, 3, '5']]],
            ['value' => '{"type":"string","const":["foo"]}'],
            ['value' => (object) ['type' => 'string', 'const' => 32.4]],
            ['value' => '{"type":"string","knownValues":[2,3,"4"]}'],
            ['value' => (object) ['type' => 'string', 'knownValues' => [1, 3, '5']]],
            ['value' => '{"type":"string","description":true}'],
            ['value' => (object) ['type' => 'string', 'description' => false]],
        ];
    }
}
