<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexTokenParser;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Atproto\Lexicon\Types\LexToken;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function is_string;
use function json_encode;

use const JSON_UNESCAPED_SLASHES;

class LexTokenParserTest extends TestCase
{
    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexTokenParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexToken::class, $parsed);
        $this->assertSame(LexUserTypeType::Token, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    #[DataProvider('invalidValuesProvider')]
    public function testThrowsForInvalidValues(object | string $value): void
    {
        $parser = new LexTokenParser();

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
            ['value' => '{"type":"blob","description":false}'],
            ['value' => (object) ['type' => 'blob', 'description' => false]],
        ];
    }
}
