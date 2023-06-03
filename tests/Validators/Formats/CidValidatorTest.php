<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators\Formats;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\Formats\CidValidator;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class CidValidatorTest extends TestCase
{
    #[DataProvider('validTestProvider')]
    public function testValidValue(string $value): void
    {
        $this->assertSame($value, (new CidValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(mixed $value, string $error): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($error);

        (new CidValidator())->validate($value);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validTestProvider(): array
    {
        return [
            ['need to actually validate a CID and not just a string'],
        ];
    }

    /**
     * @return array<array{0: mixed, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            [1234, 'Value must be a CID string'],
            [12.34, 'Value must be a CID string'],
            [false, 'Value must be a CID string'],
            [[], 'Value must be a CID string'],
            [(object) [], 'Value must be a CID string'],
            [null, 'Value must be a CID string'],
            ['', 'Value must be a CID string'],
        ];
    }
}
