<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcErrorParser;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;

class LexXrpcErrorParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexXrpcErrorParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexXrpcErrorParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexXrpcError::class, $parsed);
        $this->assertSame($checkValues['name'], $parsed->name);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"name":"FooError"}',
                'checkValues' => ['name' => 'FooError'],
            ],
            'object without properties' => [
                'value' => (object) ['name' => 'FooError'],
                'checkValues' => ['name' => 'FooError'],
            ],
            'JSON with description' => [
                'value' => '{"name":"BarError","description":"Hello there"}',
                'checkValues' => ['name' => 'BarError', 'description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['name' => 'BazError', 'description' => 'Hello there'],
                'checkValues' => ['name' => 'BazError', 'description' => 'Hello there'],
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
            ['value' => '{"name":123}'],
            ['value' => (object) ['name' => false]],
            ['value' => '{"name":"FooError","description":false}'],
            ['value' => (object) ['name' => 'FooError', 'description' => false]],
        ];
    }
}
