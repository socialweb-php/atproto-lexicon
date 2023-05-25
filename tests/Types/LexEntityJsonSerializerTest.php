<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Types;

use JsonSerializable;
use SocialWeb\Atproto\Lexicon\Types\LexEntityJsonSerializer;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function json_encode;

class LexEntityJsonSerializerTest extends TestCase
{
    public function testJsonSerializeDoesNotFilterOutFalsyValues(): void
    {
        $sut = new class () implements JsonSerializable {
            use LexEntityJsonSerializer;

            public ?string $foo = null;
            public ?int $bar = null;
            public ?bool $baz = null;

            /** @var mixed[] | null */
            public ?array $qux = null;

            /** @var array<string, mixed> */
            public array $defs = [];

            /** @var array<string, mixed> */
            public array $refs = [];

            /** @var array<string, mixed> */
            public array $properties = [];
        };

        $sut->foo = '';
        $sut->bar = 0;
        $sut->baz = false;
        $sut->qux = [];

        $this->assertJsonStringEqualsJsonString(
            '{"foo":"","bar":0,"baz":false,"qux":[],"defs":{},"refs":{},"properties":{}}',
            (string) json_encode($sut),
        );
    }

    public function testJsonSerializeFiltersOutNullValues(): void
    {
        $sut = new class () implements JsonSerializable {
            use LexEntityJsonSerializer;

            public ?string $foo = null;
            public ?int $bar = null;
            public ?bool $baz = null;

            /** @var mixed[] | null */
            public ?array $qux = null;
        };

        $sut->foo = 'This is a string';
        $sut->baz = true;

        $this->assertJsonStringEqualsJsonString(
            '{"foo":"This is a string","baz":true}',
            (string) json_encode($sut),
        );
    }

    public function testJsonSerializeReturnsEmptyObjectWhenAllValuesAreNull(): void
    {
        $sut = new class () implements JsonSerializable {
            use LexEntityJsonSerializer;

            public ?string $foo = null;
            public ?int $bar = null;
            public ?bool $baz = null;

            /** @var mixed[] | null */
            public ?array $qux = null;

            /** @var array<string, mixed> | null */
            public ?array $defs = null;

            /** @var array<string, mixed> | null */
            public ?array $refs = null;

            /** @var array<string, mixed> | null */
            public ?array $properties = null;
        };

        $this->assertJsonStringEqualsJsonString(
            '{}',
            (string) json_encode($sut),
        );
    }
}
