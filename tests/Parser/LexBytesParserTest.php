<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexBytesParser;
use SocialWeb\Atproto\Lexicon\Types\LexBytes;
use SocialWeb\Atproto\Lexicon\Types\LexType;

class LexBytesParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexBytesParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexBytesParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexBytes::class, $parsed);
        $this->assertSame(LexType::Bytes, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description ?? null);
        $this->assertSame($checkValues['maxLength'] ?? null, $parsed->maxLength);
        $this->assertSame($checkValues['minLength'] ?? null, $parsed->minLength);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON' => [
                'value' => '{"type":"bytes"}',
                'checkValues' => [],
            ],
            'object' => [
                'value' => (object) ['type' => 'bytes'],
                'checkValues' => [],
            ],
            'JSON with description' => [
                'value' => '{"type":"bytes","description":"Hi there!"}',
                'checkValues' => ['description' => 'Hi there!'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'bytes', 'description' => "What's up?"],
                'checkValues' => ['description' => "What's up?"],
            ],
            'JSON with maxLength' => [
                'value' => '{"type":"bytes","maxLength":1024}',
                'checkValues' => ['maxLength' => 1024],
            ],
            'object with maxLength' => [
                'value' => (object) ['type' => 'bytes', 'maxLength' => 2048],
                'checkValues' => ['maxLength' => 2048],
            ],
            'JSON with minLength' => [
                'value' => '{"type":"bytes","minLength":1024}',
                'checkValues' => ['minLength' => 1024],
            ],
            'object with minLength' => [
                'value' => (object) ['type' => 'bytes', 'minLength' => 2048],
                'checkValues' => ['minLength' => 2048],
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
            ['value' => '{"type":"bytes","description":false}'],
            ['value' => (object) ['type' => 'bytes', 'description' => 1234]],
            ['value' => '{"type":"bytes","maxLength":12.34}'],
            ['value' => (object) ['type' => 'bytes', 'maxLength' => 'foo']],
            ['value' => '{"type":"bytes","minLength":12.34}'],
            ['value' => (object) ['type' => 'bytes', 'minLength' => 'foo']],
        ];
    }
}
