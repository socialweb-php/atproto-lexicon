<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Types\LexBytes;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Validators\LexBytesValidator;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function assert;

class LexBytesValidatorTest extends ValidatorTestCase
{
    protected function getValidator(LexEntity $type): Validator
    {
        assert($type instanceof LexBytes);

        return new LexBytesValidator($type);
    }

    /**
     * @return array<array{0: LexBytes, 1: mixed}>
     */
    public static function validTestProvider(): array
    {
        return [
            [new LexBytes(), "\xdf\x44\x9d\x0a"],
            [new LexBytes(maxLength: 5, minLength: 3), "\xdf\x44\x9d\x0a"],
            [new LexBytes(maxLength: 4, minLength: 4), "\xdf\x44\x9d\x0a"],
        ];
    }

    /**
     * @return array<array{0: LexBytes, 1: mixed, 2: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            [new LexBytes(), true, 'Value must be a byte string'],
            [new LexBytes(), false, 'Value must be a byte string'],
            [new LexBytes(), 1234, 'Value must be a byte string'],
            [new LexBytes(), 12.34, 'Value must be a byte string'],
            [new LexBytes(), [], 'Value must be a byte string'],
            [new LexBytes(), (object) [], 'Value must be a byte string'],
            [new LexBytes(), null, 'Value must be a byte string'],
            [new LexBytes(), '', 'Value must be a byte string'],
            [new LexBytes(maxLength: 6), "\xdf\x44\x9d\x0a\xdf\x44\x9d", 'Value must not be larger than 6 bytes'],
            [new LexBytes(minLength: 4), "\xdf\x44\x9d", 'Value must not be smaller than 4 bytes'],
        ];
    }
}
