<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Validators\LexIntegerValidator;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function assert;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

class LexIntegerValidatorTest extends ValidatorTestCase
{
    protected function getValidator(LexEntity $type): Validator
    {
        assert($type instanceof LexInteger);

        return new LexIntegerValidator($type);
    }

    /**
     * @return array<array{0: LexInteger, 1: mixed}>
     */
    public static function validTestProvider(): array
    {
        return [
            [new LexInteger(), 1],
            [new LexInteger(), PHP_INT_MAX],
            [new LexInteger(), PHP_INT_MIN],
            [new LexInteger(default: 42), null],
            [new LexInteger(enum: [2, 4, 6, 8]), 4],
            [new LexInteger(maximum: 42), 42],
            [new LexInteger(minimum: -4), -4],
            [new LexInteger(const: 56), 56],
            [new LexInteger(minimum: 0, maximum: 18, enum: [2, 4, 6, 8], const: 6), 6],
        ];
    }

    /**
     * @return array<array{0: LexInteger, 1: mixed, 2: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            [new LexInteger(), 'foo', 'Value must be an integer'],
            [new LexInteger(), 12.34, 'Value must be an integer'],
            [new LexInteger(), true, 'Value must be an integer'],
            [new LexInteger(), null, 'Value must be an integer'],
            [new LexInteger(), [], 'Value must be an integer'],
            [new LexInteger(), (object) [], 'Value must be an integer'],
            [new LexInteger(enum: [2, 4, 6, 8]), 5, 'Value must be one of (2|4|6|8)'],
            [new LexInteger(maximum: 42), 43, 'Value cannot be greater than 42'],
            [new LexInteger(minimum: -42), -43, 'Value cannot be less than -42'],
            [new LexInteger(const: 1_214), 31, 'Value must be 1214'],
        ];
    }
}
