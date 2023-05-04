<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexRefUnionParser;
use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;

class LexRefUnionParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexRefUnionParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexRefUnionParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexRefUnion::class, $parsed);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
        $this->assertSame($checkValues['refs'] ?? [], $parsed->refs);
        $this->assertSame($checkValues['closed'] ?? null, $parsed->closed);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"union","refs":[]}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'union', 'refs' => []],
                'checkValues' => [],
            ],
            'JSON with description' => [
                'value' => '{"type":"union","description":"A cool description","refs":[]}',
                'checkValues' => ['description' => 'A cool description'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'union', 'description' => 'A cool description', 'refs' => []],
                'checkValues' => ['description' => 'A cool description'],
            ],
            'JSON with refs' => [
                'value' => '{"type":"union","refs":["org.example.foo","org.example.bar"]}',
                'checkValues' => ['refs' => ['org.example.foo', 'org.example.bar']],
            ],
            'object with refs' => [
                'value' => (object) ['type' => 'union', 'refs' => ['org.example.foo', 'org.example.bar']],
                'checkValues' => ['refs' => ['org.example.foo', 'org.example.bar']],
            ],
            'JSON with closed' => [
                'value' => '{"type":"union","refs":[],"closed":false}',
                'checkValues' => ['closed' => false],
            ],
            'object with closed' => [
                'value' => (object) ['type' => 'union', 'refs' => [], 'closed' => true],
                'checkValues' => ['closed' => true],
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
            ['value' => '{"type":"union"}'],
            ['value' => (object) ['type' => 'union']],
            ['value' => '{"type":"union","refs":[],"description":123}'],
            ['value' => (object) ['type' => 'union', 'refs' => [], 'description' => false]],
            ['value' => '{"type":"union","refs":false}'],
            ['value' => (object) ['type' => 'union', 'refs' => 1234]],
            ['value' => '{"type":"union","refs":["io.foo.bar",123]}'],
            ['value' => (object) ['type' => 'union', 'refs' => ['io.bar.baz', false]]],
            ['value' => '{"type":"union","refs":[],"closed":"foobar"}'],
            ['value' => (object) ['type' => 'union', 'refs' => [], 'closed' => 123]],
        ];
    }
}
