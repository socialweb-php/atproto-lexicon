<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexUnknownParser;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;

class LexUnknownParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexUnknownParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexUnknownParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexUnknown::class, $parsed);
        $this->assertSame(LexUserTypeType::Unknown, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"unknown"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'unknown'],
                'checkValues' => [],
            ],
            'JSON with description' => [
                'value' => '{"type":"unknown","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'unknown', 'description' => 'Hello there'],
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
            ['value' => '{"type":"unknown","description":false}'],
            ['value' => (object) ['type' => 'unknown', 'description' => false]],
        ];
    }
}
