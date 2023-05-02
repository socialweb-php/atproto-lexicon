<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\LexBlobParser;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function is_string;
use function json_encode;

use const JSON_UNESCAPED_SLASHES;

class LexBlobParserTest extends TestCase
{
    /**
     * @param array<string, scalar | scalar[]> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $parser = new LexBlobParser();
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexBlob::class, $parsed);
        $this->assertSame(LexUserTypeType::Blob, $parsed->type);
        $this->assertSame($checkValues['accept'] ?? null, $parsed->accept);
        $this->assertSame($checkValues['maxSize'] ?? null, $parsed->maxSize);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);
    }

    #[DataProvider('invalidValuesProvider')]
    public function testThrowsForInvalidValues(object | string $value): void
    {
        $parser = new LexBlobParser();

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
                'value' => '{"type":"blob"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'blob'],
                'checkValues' => [],
            ],
            'JSON with accept' => [
                'value' => '{"type":"blob","accept":["audio/vorbis","audio/mpeg"]}',
                'checkValues' => ['accept' => ['audio/vorbis', 'audio/mpeg']],
            ],
            'object with accept' => [
                'value' => (object) ['type' => 'blob', 'accept' => ['audio/vorbis','audio/mpeg']],
                'checkValues' => ['accept' => ['audio/vorbis', 'audio/mpeg']],
            ],
            'JSON with maxSize as int' => [
                'value' => '{"type":"blob","maxSize":1234}',
                'checkValues' => ['maxSize' => 1234],
            ],
            'object with maxSize as int' => [
                'value' => (object) ['type' => 'blob', 'maxSize' => 1234],
                'checkValues' => ['maxSize' => 1234],
            ],
            'JSON with maxSize as float' => [
                'value' => '{"type":"blob","maxSize":1234.56}',
                'checkValues' => ['maxSize' => 1234.56],
            ],
            'object with maxSize as float' => [
                'value' => (object) ['type' => 'blob', 'maxSize' => 1234.56],
                'checkValues' => ['maxSize' => 1234.56],
            ],
            'JSON with description' => [
                'value' => '{"type":"blob","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'blob', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
            'JSON with all values' => [
                'value' => '{"type":"blob","accept":["audio/vorbis"],"maxSize":123,"description":"Well then"}',
                'checkValues' => [
                    'accept' => ['audio/vorbis'],
                    'maxSize' => 123,
                    'description' => 'Well then',
                ],
            ],
            'object with all values' => [
                'value' => (object) [
                    'type' => 'blob',
                    'accept' => ['audio/vorbis'],
                    'maxSize' => 123,
                    'description' => 'Well then',
                ],
                'checkValues' => [
                    'accept' => ['audio/vorbis'],
                    'maxSize' => 123,
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
            ['value' => '{"type":"blob","accept":"audio/vorbis"}'],
            ['value' => (object) ['type' => 'blob', 'accept' => 'audio/vorbis']],
            ['value' => '{"type":"blob","accept":["audio/vorbis",123]}'],
            ['value' => (object) ['type' => 'blob', 'accept' => ['audio/vorbis', 123]]],
            ['value' => '{"type":"blob","accept":["audio/vorbis"],"maxSize":"123"}'],
            ['value' => (object) ['type' => 'blob', 'accept' => ['audio/vorbis'], 'maxSize' => '123']],
            ['value' => '{"type":"blob","accept":["audio/vorbis"],"maxSize":123,"description":false}'],
            ['value' =>
                (object) [
                    'type' => 'blob', 'accept' => ['audio/vorbis'], 'maxSize' => 123, 'description' => false,
                ],
            ],
        ];
    }
}
