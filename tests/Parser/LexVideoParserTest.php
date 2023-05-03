<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexVideoParser;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;
use SocialWeb\Atproto\Lexicon\Types\LexVideo;

class LexVideoParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexVideoParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexVideoParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexVideo::class, $parsed);
        $this->assertSame(LexUserTypeType::Video, $parsed->type);
        $this->assertSame($checkValues['accept'] ?? null, $parsed->accept);
        $this->assertSame($checkValues['maxSize'] ?? null, $parsed->maxSize);
        $this->assertSame($checkValues['maxWidth'] ?? null, $parsed->maxWidth);
        $this->assertSame($checkValues['maxHeight'] ?? null, $parsed->maxHeight);
        $this->assertSame($checkValues['maxLength'] ?? null, $parsed->maxLength);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[]>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"video"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'video'],
                'checkValues' => [],
            ],
            'JSON with accept' => [
                'value' => '{"type":"video","accept":["video/mpeg","video/ogg"]}',
                'checkValues' => ['accept' => ['video/mpeg', 'video/ogg']],
            ],
            'object with accept' => [
                'value' => (object) ['type' => 'video', 'accept' => ['video/mpeg', 'video/ogg']],
                'checkValues' => ['accept' => ['video/mpeg', 'video/ogg']],
            ],
            'JSON with maxSize as int' => [
                'value' => '{"type":"video","maxSize":1234}',
                'checkValues' => ['maxSize' => 1234],
            ],
            'object with maxSize as int' => [
                'value' => (object) ['type' => 'video', 'maxSize' => 1234],
                'checkValues' => ['maxSize' => 1234],
            ],
            'JSON with maxSize as float' => [
                'value' => '{"type":"video","maxSize":1234.56}',
                'checkValues' => ['maxSize' => 1234.56],
            ],
            'object with maxSize as float' => [
                'value' => (object) ['type' => 'video', 'maxSize' => 1234.56],
                'checkValues' => ['maxSize' => 1234.56],
            ],
            'JSON with maxWidth as int' => [
                'value' => '{"type":"video","maxWidth":5678}',
                'checkValues' => ['maxWidth' => 5678],
            ],
            'object with maxWidth as int' => [
                'value' => (object) ['type' => 'video', 'maxWidth' => 5678],
                'checkValues' => ['maxWidth' => 5678],
            ],
            'JSON with maxWidth as float' => [
                'value' => '{"type":"video","maxWidth":5678.91}',
                'checkValues' => ['maxWidth' => 5678.91],
            ],
            'object with maxWidth as float' => [
                'value' => (object) ['type' => 'video', 'maxWidth' => 5678.91],
                'checkValues' => ['maxWidth' => 5678.91],
            ],
            'JSON with maxHeight as int' => [
                'value' => '{"type":"video","maxHeight":5678}',
                'checkValues' => ['maxHeight' => 5678],
            ],
            'object with maxHeight as int' => [
                'value' => (object) ['type' => 'video', 'maxHeight' => 5678],
                'checkValues' => ['maxHeight' => 5678],
            ],
            'JSON with maxHeight as float' => [
                'value' => '{"type":"video","maxHeight":5678.91}',
                'checkValues' => ['maxHeight' => 5678.91],
            ],
            'object with maxHeight as float' => [
                'value' => (object) ['type' => 'video', 'maxHeight' => 5678.91],
                'checkValues' => ['maxHeight' => 5678.91],
            ],
            'JSON with maxLength as int' => [
                'value' => '{"type":"video","maxLength":100234}',
                'checkValues' => ['maxLength' => 100234],
            ],
            'object with maxLength as int' => [
                'value' => (object) ['type' => 'video', 'maxLength' => 5678],
                'checkValues' => ['maxLength' => 5678],
            ],
            'JSON with maxLength as float' => [
                'value' => '{"type":"video","maxLength":5678.91}',
                'checkValues' => ['maxLength' => 5678.91],
            ],
            'object with maxLength as float' => [
                'value' => (object) ['type' => 'video', 'maxLength' => 5678.91],
                'checkValues' => ['maxLength' => 5678.91],
            ],
            'JSON with description' => [
                'value' => '{"type":"video","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'video', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'JSON with all values' => [
                'value' => '{"type":"video","accept":["video/mpeg"],"maxSize":123,'
                    . '"maxWidth":456,"maxHeight":789,"maxLength":102,"description":"Well then"}',
                'checkValues' => [
                    'accept' => ['video/mpeg'],
                    'maxSize' => 123,
                    'maxWidth' => 456,
                    'maxHeight' => 789,
                    'maxLength' => 102,
                    'description' => 'Well then',
                ],
            ],
            'object with all values' => [
                'value' => (object) [
                    'type' => 'video',
                    'accept' => ['video/ogg'],
                    'maxSize' => 123,
                    'maxWidth' => 456,
                    'maxHeight' => 789,
                    'maxLength' => 102,
                    'description' => 'Well then',
                ],
                'checkValues' => [
                    'accept' => ['video/ogg'],
                    'maxSize' => 123,
                    'maxWidth' => 456,
                    'maxHeight' => 789,
                    'maxLength' => 102,
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
            ['value' => '{"type":"video","accept":"video/mpeg"}'],
            ['value' => (object) ['type' => 'video', 'accept' => 'video/mpeg']],
            ['value' => '{"type":"video","accept":["video/mpeg",123]}'],
            ['value' => (object) ['type' => 'video', 'accept' => ['video/mpeg', 123]]],
            ['value' => '{"type":"video","maxSize":"123"}'],
            ['value' => (object) ['type' => 'video', 'maxSize' => '123']],
            ['value' => '{"type":"video","maxWidth":"456"}'],
            ['value' => (object) ['type' => 'video', 'maxWidth' => '456']],
            ['value' => '{"type":"video","maxHeight":"789"}'],
            ['value' => (object) ['type' => 'video', 'maxHeight' => '789']],
            ['value' => '{"type":"video","maxLength":"102"}'],
            ['value' => (object) ['type' => 'video', 'maxLength' => '102']],
            ['value' => '{"type":"video","description":false}'],
            ['value' => (object) ['type' => 'video', 'description' => false]],
        ];
    }
}
