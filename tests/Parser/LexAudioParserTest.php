<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexAudioParser;
use SocialWeb\Atproto\Lexicon\Types\LexAudio;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;

class LexAudioParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexAudioParser::class;
    }

    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexAudioParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexAudio::class, $parsed);
        $this->assertSame(LexUserTypeType::Audio, $parsed->type);
        $this->assertSame($checkValues['accept'] ?? null, $parsed->accept);
        $this->assertSame($checkValues['maxSize'] ?? null, $parsed->maxSize);
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
                'value' => '{"type":"audio"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'audio'],
                'checkValues' => [],
            ],
            'JSON with accept' => [
                'value' => '{"type":"audio","accept":["audio/vorbis","audio/mpeg"]}',
                'checkValues' => ['accept' => ['audio/vorbis', 'audio/mpeg']],
            ],
            'object with accept' => [
                'value' => (object) ['type' => 'audio', 'accept' => ['audio/vorbis','audio/mpeg']],
                'checkValues' => ['accept' => ['audio/vorbis', 'audio/mpeg']],
            ],
            'JSON with maxSize as int' => [
                'value' => '{"type":"audio","maxSize":1234}',
                'checkValues' => ['maxSize' => 1234],
            ],
            'object with maxSize as int' => [
                'value' => (object) ['type' => 'audio', 'maxSize' => 1234],
                'checkValues' => ['maxSize' => 1234],
            ],
            'JSON with maxSize as float' => [
                'value' => '{"type":"audio","maxSize":1234.56}',
                'checkValues' => ['maxSize' => 1234.56],
            ],
            'object with maxSize as float' => [
                'value' => (object) ['type' => 'audio', 'maxSize' => 1234.56],
                'checkValues' => ['maxSize' => 1234.56],
            ],
            'JSON with maxLength as int' => [
                'value' => '{"type":"audio","maxLength":5678}',
                'checkValues' => ['maxLength' => 5678],
            ],
            'object with maxLength as int' => [
                'value' => (object) ['type' => 'audio', 'maxLength' => 5678],
                'checkValues' => ['maxLength' => 5678],
            ],
            'JSON with maxLength as float' => [
                'value' => '{"type":"audio","maxLength":5678.91}',
                'checkValues' => ['maxLength' => 5678.91],
            ],
            'object with maxLength as float' => [
                'value' => (object) ['type' => 'audio', 'maxLength' => 5678.91],
                'checkValues' => ['maxLength' => 5678.91],
            ],
            'JSON with description' => [
                'value' => '{"type":"audio","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'audio', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'JSON with all values' => [
                'value' => '{"type":"audio","accept":["audio/vorbis"],"maxSize":123,'
                    . '"maxLength":456,"description":"Well then"}',
                'checkValues' => [
                    'accept' => ['audio/vorbis'],
                    'maxSize' => 123,
                    'maxLength' => 456,
                    'description' => 'Well then',
                ],
            ],
            'object with all values' => [
                'value' => (object) [
                    'type' => 'audio',
                    'accept' => ['audio/vorbis'],
                    'maxSize' => 123,
                    'maxLength' => 456,
                    'description' => 'Well then',
                ],
                'checkValues' => [
                    'accept' => ['audio/vorbis'],
                    'maxSize' => 123,
                    'maxLength' => 456,
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
            ['value' => '{"type":"audio","accept":"audio/vorbis"}'],
            ['value' => (object) ['type' => 'audio', 'accept' => 'audio/vorbis']],
            ['value' => '{"type":"audio","accept":["audio/vorbis",123]}'],
            ['value' => (object) ['type' => 'audio', 'accept' => ['audio/vorbis', 123]]],
            ['value' => '{"type":"audio","accept":["audio/vorbis"],"maxSize":"123"}'],
            ['value' => (object) ['type' => 'audio', 'accept' => ['audio/vorbis'], 'maxSize' => '123']],
            ['value' => '{"type":"audio","accept":["audio/vorbis"],"maxSize":123,"maxLength":"456"}'],
            ['value' =>
                (object) ['type' => 'audio', 'accept' => ['audio/vorbis'], 'maxSize' => 123, 'maxLength' => '456'],
            ],
            ['value' => '{"type":"audio","accept":["audio/vorbis"],"maxSize":123,"maxLength":456,"description":false}'],
            ['value' =>
                (object) [
                    'type' => 'audio', 'accept' => ['audio/vorbis'], 'maxSize' => 123,
                    'maxLength' => 456, 'description' => false,
                ],
            ],
        ];
    }
}
