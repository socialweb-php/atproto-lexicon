<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexCidLinkParser;
use SocialWeb\Atproto\Lexicon\Types\LexCidLink;
use SocialWeb\Atproto\Lexicon\Types\LexType;

class LexCidLinkParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexCidLinkParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexCidLinkParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexCidLink::class, $parsed);
        $this->assertSame(LexType::CidLink, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description ?? null);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON' => [
                'value' => '{"type":"cid-link"}',
                'checkValues' => [],
            ],
            'object' => [
                'value' => (object) ['type' => 'cid-link'],
                'checkValues' => [],
            ],
            'JSON with description' => [
                'value' => '{"type":"cid-link","description":"Hi there!"}',
                'checkValues' => ['description' => 'Hi there!'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'cid-link', 'description' => "What's up?"],
                'checkValues' => ['description' => "What's up?"],
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
            ['value' => '{"type":"cid-link","description":false}'],
            ['value' => (object) ['type' => 'cid-link', 'description' => 1234]],
        ];
    }
}
