<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators\Formats;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\Formats\UriValidator;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class UriValidatorTest extends TestCase
{
    #[DataProvider('validTestProvider')]
    public function testValidValue(string $value): void
    {
        $this->assertSame($value, (new UriValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(string $value, string $error): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($error);

        (new UriValidator())->validate($value);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validTestProvider(): array
    {
        return [
            ['foo:bar'],
            ['foo://bar'],
        ];
    }

    /**
     * @return array<array{0: string, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            ['foobar', 'Value must be a URI'],
            ['foo://b ar', 'Value must be a URI'],
        ];
    }
}
