<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators\Formats;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\Formats\AtIdentifierValidator;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class AtIdentifierValidatorTest extends TestCase
{
    #[DataProvider('validTestProvider')]
    public function testValidValue(string $value): void
    {
        $this->assertSame($value, (new AtIdentifierValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(string $value, string $error): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($error);

        (new AtIdentifierValidator())->validate($value);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validTestProvider(): array
    {
        return [
            ['bsky.test'],
            ['did:plc:12345678abcdefghijklmnop'],
        ];
    }

    /**
     * @return array<array{0: string, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            ['bad id', 'Value must be a valid handle or DID'],
            ['-bad-.test', 'Value must be a valid handle or DID'],
        ];
    }
}
