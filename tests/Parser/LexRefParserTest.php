<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexRefParser;
use SocialWeb\Atproto\Lexicon\Types\LexRef;

class LexRefParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexRefParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexRefParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexRef::class, $parsed);
        $this->assertSame($checkValues['ref'], $parsed->ref);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON' => [
                'value' => '{"type":"ref","ref":"net.example.foo.something#somewhere"}',
                'checkValues' => ['ref' => 'net.example.foo.something#somewhere'],
            ],
            'object' => [
                'value' => (object) ['type' => 'ref', 'ref' => 'net.example.foo.something#somewhere'],
                'checkValues' => ['ref' => 'net.example.foo.something#somewhere'],
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
            ['value' => '{"type":"ref"}'],
            ['value' => (object) ['type' => 'ref']],
            ['value' => '{"type":"ref","ref":1234}'],
            ['value' => (object) ['type' => 'ref', 'ref' => null]],
        ];
    }
}
