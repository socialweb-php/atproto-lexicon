<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexImageParser;
use SocialWeb\Atproto\Lexicon\Types\LexImage;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;

class LexImageParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexImageParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexImageParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexImage::class, $parsed);
        $this->assertSame(LexUserTypeType::Image, $parsed->type);
        $this->assertSame($checkValues['accept'] ?? null, $parsed->accept);
        $this->assertSame($checkValues['maxSize'] ?? null, $parsed->maxSize);
        $this->assertSame($checkValues['maxWidth'] ?? null, $parsed->maxWidth);
        $this->assertSame($checkValues['maxHeight'] ?? null, $parsed->maxHeight);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"image"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'image'],
                'checkValues' => [],
            ],
            'JSON with accept' => [
                'value' => '{"type":"image","accept":["image/jpeg","image/png"]}',
                'checkValues' => ['accept' => ['image/jpeg', 'image/png']],
            ],
            'object with accept' => [
                'value' => (object) ['type' => 'image', 'accept' => ['image/jpeg', 'image/png']],
                'checkValues' => ['accept' => ['image/jpeg', 'image/png']],
            ],
            'JSON with maxSize as int' => [
                'value' => '{"type":"image","maxSize":1234}',
                'checkValues' => ['maxSize' => 1234],
            ],
            'object with maxSize as int' => [
                'value' => (object) ['type' => 'image', 'maxSize' => 1234],
                'checkValues' => ['maxSize' => 1234],
            ],
            'JSON with maxSize as float' => [
                'value' => '{"type":"image","maxSize":1234.56}',
                'checkValues' => ['maxSize' => 1234.56],
            ],
            'object with maxSize as float' => [
                'value' => (object) ['type' => 'image', 'maxSize' => 1234.56],
                'checkValues' => ['maxSize' => 1234.56],
            ],
            'JSON with maxWidth as int' => [
                'value' => '{"type":"image","maxWidth":5678}',
                'checkValues' => ['maxWidth' => 5678],
            ],
            'object with maxWidth as int' => [
                'value' => (object) ['type' => 'image', 'maxWidth' => 5678],
                'checkValues' => ['maxWidth' => 5678],
            ],
            'JSON with maxWidth as float' => [
                'value' => '{"type":"image","maxWidth":5678.91}',
                'checkValues' => ['maxWidth' => 5678.91],
            ],
            'object with maxWidth as float' => [
                'value' => (object) ['type' => 'image', 'maxWidth' => 5678.91],
                'checkValues' => ['maxWidth' => 5678.91],
            ],
            'JSON with maxHeight as int' => [
                'value' => '{"type":"image","maxHeight":5678}',
                'checkValues' => ['maxHeight' => 5678],
            ],
            'object with maxHeight as int' => [
                'value' => (object) ['type' => 'image', 'maxHeight' => 5678],
                'checkValues' => ['maxHeight' => 5678],
            ],
            'JSON with maxHeight as float' => [
                'value' => '{"type":"image","maxHeight":5678.91}',
                'checkValues' => ['maxHeight' => 5678.91],
            ],
            'object with maxHeight as float' => [
                'value' => (object) ['type' => 'image', 'maxHeight' => 5678.91],
                'checkValues' => ['maxHeight' => 5678.91],
            ],
            'JSON with description' => [
                'value' => '{"type":"image","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'image', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'JSON with all values' => [
                'value' => '{"type":"image","accept":["image/png"],"maxSize":123,'
                    . '"maxWidth":456,"maxHeight":789,"description":"Well then"}',
                'checkValues' => [
                    'accept' => ['image/png'],
                    'maxSize' => 123,
                    'maxWidth' => 456,
                    'maxHeight' => 789,
                    'description' => 'Well then',
                ],
            ],
            'object with all values' => [
                'value' => (object) [
                    'type' => 'image',
                    'accept' => ['image/png'],
                    'maxSize' => 123,
                    'maxWidth' => 456,
                    'maxHeight' => 789,
                    'description' => 'Well then',
                ],
                'checkValues' => [
                    'accept' => ['image/png'],
                    'maxSize' => 123,
                    'maxWidth' => 456,
                    'maxHeight' => 789,
                    'description' => 'Well then',
                ],
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
            ['value' => '{"type":"image","accept":"image/jpeg"}'],
            ['value' => (object) ['type' => 'image', 'accept' => 'image/jpeg']],
            ['value' => '{"type":"image","accept":["image/jpeg",123]}'],
            ['value' => (object) ['type' => 'image', 'accept' => ['image/jpeg', 123]]],
            ['value' => '{"type":"image","accept":["image/jpeg"],"maxSize":"123"}'],
            ['value' => (object) ['type' => 'image', 'accept' => ['image/jpeg'], 'maxSize' => '123']],
            ['value' => '{"type":"image","accept":["image/jpeg"],"maxSize":123,"maxWidth":"456"}'],
            ['value' =>
                (object) ['type' => 'image', 'accept' => ['image/jpeg'], 'maxSize' => 123, 'maxWidth' => '456'],
            ],
            ['value' => '{"type":"image","accept":["image/jpeg"],"maxSize":123,"maxWidth":456,"maxHeight":"789"}'],
            ['value' =>
                (object) [
                    'type' => 'image', 'accept' => ['image/jpeg'], 'maxSize' => 123,
                    'maxWidth' => 456, 'maxHeight' => '789',
                ],
            ],
            ['value' =>
                '{"type":"image","accept":["image/jpeg"],"maxSize":123,'
                    . '"maxWidth":456,"maxHeight":789,"description":false}',
            ],
            ['value' =>
                (object) [
                    'type' => 'image', 'accept' => ['image/jpeg'], 'maxSize' => 123,
                    'maxWidth' => 456, 'maxHeight' => 789, 'description' => false,
                ],
            ],
        ];
    }
}
