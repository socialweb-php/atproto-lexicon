<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexIntegerParser;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveType;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function is_string;
use function json_encode;

use const JSON_UNESCAPED_SLASHES;

class LexIntegerParserTest extends TestCase
{
    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexIntegerParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexInteger::class, $parsed);
        $this->assertSame(LexPrimitiveType::Integer, $parsed->type);
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
        $parser = new LexIntegerParser();

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
                'value' => '{"type":"integer"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'integer'],
                'checkValues' => [],
            ],
            'JSON with default as int' => [
                'value' => '{"type":"integer","default":1234}',
                'checkValues' => ['default' => 1234],
            ],
            'object with default as int' => [
                'value' => (object) ['type' => 'integer', 'default' => 1234],
                'checkValues' => ['default' => 1234],
            ],
            'JSON with minimum as int' => [
                'value' => '{"type":"integer","minimum":3}',
                'checkValues' => ['minimum' => 3],
            ],
            'object with minimum as int' => [
                'value' => (object) ['type' => 'integer', 'minimum' => 3],
                'checkValues' => ['minimum' => 3],
            ],
            'JSON with maximum as int' => [
                'value' => '{"type":"integer","maximum":1001}',
                'checkValues' => ['maximum' => 1001],
            ],
            'object with maximum as int' => [
                'value' => (object) ['type' => 'integer', 'maximum' => 1002],
                'checkValues' => ['maximum' => 1002],
            ],
            'JSON with enum as int[]' => [
                'value' => '{"type":"integer","enum":[1,3,5,9]}',
                'checkValues' => ['enum' => [1, 3, 5, 9]],
            ],
            'object with enum as int[]' => [
                'value' => (object) ['type' => 'integer', 'enum' => [1, 3, 5, 9]],
                'checkValues' => ['enum' => [1, 3, 5, 9]],
            ],
            'JSON with const as int' => [
                'value' => '{"type":"integer","const":59}',
                'checkValues' => ['const' => 59],
            ],
            'object with const as int' => [
                'value' => (object) ['type' => 'integer', 'const' => 73],
                'checkValues' => ['const' => 73],
            ],
            'JSON with description' => [
                'value' => '{"type":"integer","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'integer', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'JSON with all values' => [
                'value' => '{"type":"integer","default":1234,"minimum":0,"maximum":100,'
                    . '"enum":[0,42,63,100],"const":42,"description":"Well then"}',
                'checkValues' => [
                    'default' => 1234,
                    'minimum' => 0,
                    'maximum' => 100,
                    'enum' => [0, 42, 63, 100],
                    'const' => 42,
                    'description' => 'Well then',
                ],
            ],
            'object with all values' => [
                'value' => (object) [
                    'type' => 'integer',
                    'default' => 789,
                    'minimum' => 0,
                    'maximum' => 100,
                    'enum' => [0, 42, 63, 100],
                    'const' => 42,
                    'description' => 'Well then',
                ],
                'checkValues' => [
                    'default' => 789,
                    'minimum' => 0,
                    'maximum' => 100,
                    'enum' => [0, 42, 63, 100],
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
            ['value' => '{"type":"integer","default":42.1}'],
            ['value' => (object) ['type' => 'integer', 'default' => 42.1]],
            ['value' => '{"type":"integer","default":42,"minimum":"abc"}'],
            ['value' => (object) ['type' => 'integer', 'default' => 42, 'minimum' => 56.2]],
            ['value' => '{"type":"integer","default":42,"minimum":2,"maximum":"def"}'],
            ['value' => (object) ['type' => 'integer', 'default' => 42, 'minimum' => 2, 'maximum' => 47.1]],
            ['value' => '{"type":"integer","default":42,"minimum":2,"maximum":47,"enum":[2,3,"4"]}'],
            ['value' =>
                (object) ['type' => 'integer', 'default' => 42, 'minimum' => 2, 'maximum' => 47, 'enum' => [1, 3, '5']],
            ],
            ['value' => '{"type":"integer","default":42,"minimum":2,"maximum":47,"enum":[2,3,4],"const":"ghi"}'],
            ['value' =>
                (object) [
                    'type' => 'integer', 'default' => 42, 'minimum' => 2, 'maximum' => 47,
                    'enum' => [1, 3, 5], 'const' => 32.4,
                ],
            ],
            ['value' =>
                '{"type":"integer","default":42,"minimum":2,"maximum":47,"enum":[2,3,4],"const":32,"description":true}',
            ],
            ['value' =>
                (object) [
                    'type' => 'integer', 'default' => 42, 'minimum' => 2, 'maximum' => 47,
                    'enum' => [1, 3, 5], 'const' => 32, 'description' => false,
                ],
            ],
        ];
    }
}
