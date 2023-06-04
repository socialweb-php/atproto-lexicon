<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators;

use ArrayIterator;
use ArrayObject;
use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Atproto\Lexicon\Validators\LexUnknownValidator;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class LexUnknownValidatorTest extends TestCase
{
    /**
     * @phpstan-param iterable<string, mixed> | object $value
     */
    #[DataProvider('validTestProvider')]
    public function testValidValue(iterable | object $value): void
    {
        $this->assertSame($value, (new LexUnknownValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(mixed $value, string $error): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($error);

        (new LexUnknownValidator())->validate($value);
    }

    /**
     * @return array<array{0: iterable<string, mixed> | object}>
     */
    public static function validTestProvider(): array
    {
        $dictionary = [
            'a' => 1234,
            'b' => 12.34,
            'c' => false,
            'd' => ['a', 'b', 'c'],
            'e' => [1, 2, 3],
            'f' => ['a' => 1, 'b' => 2, 'c' => 3],
            'g' => (object) ['a' => 1, 'b' => 2, 'c' => 3],
            'h' => null,
            'i' => 'foo bar',
        ];

        return [
            [$dictionary],
            [(object) $dictionary],
            [new ArrayObject($dictionary)],
            [new ArrayIterator($dictionary)],
        ];
    }

    /**
     * @return array<array{0: mixed, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        $badDictionary = ['a' => 1, 'b' => 2, 3, 'd' => 4];

        return [
            [1234, 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [12.34, 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [false, 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [['a', 'b', 'c'], 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [(object) ['a', 'b', 'c'], 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [null, 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            ['', 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [$badDictionary, 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [(object) $badDictionary, 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [new ArrayObject($badDictionary), 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
            [new ArrayIterator($badDictionary), 'Value must be a dictionary of key-value pairs, i.e., a JSON object'],
        ];
    }
}
