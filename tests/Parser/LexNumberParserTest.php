<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexNumberParser;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Atproto\Lexicon\Types\LexNumber;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveType;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function is_string;
use function json_encode;

use const JSON_UNESCAPED_SLASHES;

class LexNumberParserTest extends TestCase
{
    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexNumberParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexNumber::class, $parsed);
        $this->assertSame(LexPrimitiveType::Number, $parsed->type);
        $this->assertSame($checkValues['default'] ?? null, $parsed->default);
        $this->assertSame($checkValues['minimum'] ?? null, $parsed->minimum);
        $this->assertSame($checkValues['maximum'] ?? null, $parsed->maximum);
        $this->assertSame($checkValues['enum'] ?? null, $parsed->enum);
        $this->assertSame($checkValues['const'] ?? null, $parsed->const);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    #[DataProvider('invalidValuesProvider')]
    public function testThrowsForInvalidValues(object | string $value): void
    {
        $parser = new LexNumberParser();

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
                'value' => '{"type":"number"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'number'],
                'checkValues' => [],
            ],
            'JSON with default as int' => [
                'value' => '{"type":"number","default":1234}',
                'checkValues' => ['default' => 1234],
            ],
            'object with default as int' => [
                'value' => (object) ['type' => 'number', 'default' => 1234],
                'checkValues' => ['default' => 1234],
            ],
            'JSON with default as float' => [
                'value' => '{"type":"number","default":12.34}',
                'checkValues' => ['default' => 12.34],
            ],
            'object with default as float' => [
                'value' => (object) ['type' => 'number', 'default' => 12.34],
                'checkValues' => ['default' => 12.34],
            ],
            'JSON with minimum as int' => [
                'value' => '{"type":"number","minimum":3}',
                'checkValues' => ['minimum' => 3],
            ],
            'object with minimum as int' => [
                'value' => (object) ['type' => 'number', 'minimum' => 3],
                'checkValues' => ['minimum' => 3],
            ],
            'JSON with minimum as float' => [
                'value' => '{"type":"number","minimum":3.1}',
                'checkValues' => ['minimum' => 3.1],
            ],
            'object with minimum as float' => [
                'value' => (object) ['type' => 'number', 'minimum' => 3.3],
                'checkValues' => ['minimum' => 3.3],
            ],
            'JSON with maximum as int' => [
                'value' => '{"type":"number","maximum":1001}',
                'checkValues' => ['maximum' => 1001],
            ],
            'object with maximum as int' => [
                'value' => (object) ['type' => 'number', 'maximum' => 1002],
                'checkValues' => ['maximum' => 1002],
            ],
            'JSON with maximum as float' => [
                'value' => '{"type":"number","maximum":1001.001}',
                'checkValues' => ['maximum' => 1001.001],
            ],
            'object with maximum as float' => [
                'value' => (object) ['type' => 'number', 'maximum' => 1002.023],
                'checkValues' => ['maximum' => 1002.023],
            ],
            'JSON with enum as float[]|int[]' => [
                'value' => '{"type":"number","enum":[1,3.4,5,9.2]}',
                'checkValues' => ['enum' => [1, 3.4, 5, 9.2]],
            ],
            'object with enum as float[]|int[]' => [
                'value' => (object) ['type' => 'number', 'enum' => [1, 3, 5.9, 9]],
                'checkValues' => ['enum' => [1, 3, 5.9, 9]],
            ],
            'JSON with const as int' => [
                'value' => '{"type":"number","const":59}',
                'checkValues' => ['const' => 59],
            ],
            'object with const as int' => [
                'value' => (object) ['type' => 'number', 'const' => 73],
                'checkValues' => ['const' => 73],
            ],
            'JSON with const as float' => [
                'value' => '{"type":"number","const":59.987}',
                'checkValues' => ['const' => 59.987],
            ],
            'object with const as float' => [
                'value' => (object) ['type' => 'number', 'const' => 73.0001],
                'checkValues' => ['const' => 73.0001],
            ],
            'JSON with description' => [
                'value' => '{"type":"number","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'number', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'JSON with all values' => [
                'value' => '{"type":"number","default":1234,"minimum":0.01,"maximum":100,'
                    . '"enum":[0,42,63.2,100],"const":42.1,"description":"Well then"}',
                'checkValues' => [
                    'default' => 1234,
                    'minimum' => 0.01,
                    'maximum' => 100,
                    'enum' => [0, 42, 63.2, 100],
                    'const' => 42.1,
                    'description' => 'Well then',
                ],
            ],
            'object with all values' => [
                'value' => (object) [
                    'type' => 'number',
                    'default' => 78.9,
                    'minimum' => 0,
                    'maximum' => 100.89,
                    'enum' => [0.23, 42, 63, 100],
                    'const' => 42,
                    'description' => 'Well then',
                ],
                'checkValues' => [
                    'default' => 78.9,
                    'minimum' => 0,
                    'maximum' => 100.89,
                    'enum' => [0.23, 42, 63, 100],
                    'const' => 42,
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
            ['value' => '{"type":"number","default":"42.1"}'],
            ['value' => (object) ['type' => 'number', 'default' => '42.1']],
            ['value' => '{"type":"number","default":42,"minimum":"abc"}'],
            ['value' => (object) ['type' => 'number', 'default' => 42, 'minimum' => '56.2']],
            ['value' => '{"type":"number","default":42,"minimum":2,"maximum":"def"}'],
            ['value' => (object) ['type' => 'number', 'default' => 42, 'minimum' => 2, 'maximum' => '47.1']],
            ['value' => '{"type":"number","default":42,"minimum":2,"maximum":47,"enum":[2,3,"4"]}'],
            ['value' =>
                (object) ['type' => 'number', 'default' => 42, 'minimum' => 2, 'maximum' => 47, 'enum' => [1, 3, '5']],
            ],
            ['value' => '{"type":"number","default":42,"minimum":2,"maximum":47,"enum":[2,3,4],"const":"ghi"}'],
            ['value' =>
                (object) [
                    'type' => 'number', 'default' => 42, 'minimum' => 2, 'maximum' => 47,
                    'enum' => [1, 3, 5], 'const' => false,
                ],
            ],
            ['value' =>
                '{"type":"number","default":42,"minimum":2,"maximum":47,"enum":[2,3,4],"const":32,"description":true}',
            ],
            ['value' =>
                (object) [
                    'type' => 'number', 'default' => 42, 'minimum' => 2, 'maximum' => 47,
                    'enum' => [1, 3, 5], 'const' => 32, 'description' => false,
                ],
            ],
        ];
    }
}
