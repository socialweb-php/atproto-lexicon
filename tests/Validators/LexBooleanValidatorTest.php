<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Types\LexBoolean;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Validators\LexBooleanValidator;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function assert;

class LexBooleanValidatorTest extends ValidatorTestCase
{
    protected function getValidator(LexEntity $type): Validator
    {
        assert($type instanceof LexBoolean);

        return new LexBooleanValidator($type);
    }

    /**
     * @return array<array{0: LexBoolean, 1: mixed}>
     */
    public static function validTestProvider(): array
    {
        return [
            [new LexBoolean(), true],
            [new LexBoolean(), false],
            [new LexBoolean(default: true), null],
            [new LexBoolean(default: false), null],
            [new LexBoolean(const: true), true],
            [new LexBoolean(const: false), false],
        ];
    }

    /**
     * @return array<array{0: LexBoolean, 1: mixed, 2: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            [new LexBoolean(), 'foobar', 'Value must be a boolean'],
            [new LexBoolean(), 1234, 'Value must be a boolean'],
            [new LexBoolean(), 12.34, 'Value must be a boolean'],
            [new LexBoolean(), [], 'Value must be a boolean'],
            [new LexBoolean(), (object) [], 'Value must be a boolean'],
            [new LexBoolean(), null, 'Value must be a boolean'],
            [new LexBoolean(const: true), false, 'Value must be true'],
            [new LexBoolean(const: false), true, 'Value must be false'],
            [new LexBoolean(default: false, const: true), null, 'Value must be true'],
            [new LexBoolean(default: true, const: false), null, 'Value must be false'],
        ];
    }
}
