<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexTokenParser;
use SocialWeb\Atproto\Lexicon\Types\LexToken;
use SocialWeb\Atproto\Lexicon\Types\LexType;

class LexTokenParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexTokenParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexTokenParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexToken::class, $parsed);
        $this->assertSame(LexType::Token, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"token"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'token'],
                'checkValues' => [],
            ],
            'JSON with description' => [
                'value' => '{"type":"token","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'token', 'description' => 'Hello there'],
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
            ['value' => '{"type":"token","description":false}'],
            ['value' => (object) ['type' => 'token', 'description' => false]],
        ];
    }
}
