<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexPrimitiveArrayParser;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveArray;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;

use function json_encode;

class LexPrimitiveArrayParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexPrimitiveArrayParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexPrimitiveArrayParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexPrimitiveArray::class, $parsed);
        $this->assertSame(LexType::Array, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
        $this->assertSame($checkValues['minLength'] ?? null, $parsed->minLength);
        $this->assertSame($checkValues['maxLength'] ?? null, $parsed->maxLength);

        // Compare as JSON strings to avoid problems where objects in the parsed
        // values fail equality checks due to the parser factory instances they
        // contain in private properties.
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['items'] ?? null),
            (string) json_encode($parsed->items),
        );

        if ($parsed->items !== null) {
            $this->assertSame($parsed, $parsed->items->getParent());
        }
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | LexEntity>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"array"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'array'],
                'checkValues' => [],
            ],
            'JSON with items as unknown' => [
                'value' => '{"type":"array","items":{"type":"unknown"}}',
                'checkValues' => ['items' => new LexUnknown()],
            ],
            'object with items as string' => [
                'value' => (object) ['type' => 'array', 'items' => (object) ['type' => 'string']],
                'checkValues' => ['items' => new LexString()],
            ],
            'JSON with minLength as int' => [
                'value' => '{"type":"array","minLength":3}',
                'checkValues' => ['minLength' => 3],
            ],
            'object with minLength as int' => [
                'value' => (object) ['type' => 'array', 'minLength' => 3],
                'checkValues' => ['minLength' => 3],
            ],
            'JSON with maxLength as int' => [
                'value' => '{"type":"array","maxLength":1001}',
                'checkValues' => ['maxLength' => 1001],
            ],
            'object with maxLength as int' => [
                'value' => (object) ['type' => 'array', 'maxLength' => 1002],
                'checkValues' => ['maxLength' => 1002],
            ],
            'JSON with description' => [
                'value' => '{"type":"array","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'array', 'description' => 'Hello there'],
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
            ['value' => '{"type":"array","items":{"type":"token"}}'],
            ['value' => (object) ['type' => 'array', 'items' => 'abc']],
            ['value' => '{"type":"array","items":{"type":"object"}}'],
            ['value' => (object) ['type' => 'array', 'items' => (object) ['type' => 'ref', 'ref' => 'io.foo.bar']]],
            ['value' => '{"type":"array","items":{"type":"blob"}}'],
            ['value' => (object) ['type' => 'array', 'items' => (object) ['type' => 'bytes']]],
            ['value' => '{"type":"array","minLength":12.3}'],
            ['value' => (object) ['type' => 'array', 'minLength' => 'foo']],
            ['value' => '{"type":"array","maxLength":false}'],
            ['value' => (object) ['type' => 'array', 'maxLength' => [1, 2, 3]]],
            ['value' => '{"type":"array","description":false}'],
            ['value' => (object) ['type' => 'array', 'description' => false]],
        ];
    }
}
