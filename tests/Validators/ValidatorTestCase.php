<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Atproto\Lexicon\Validators\Validator;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

abstract class ValidatorTestCase extends TestCase
{
    abstract protected function getValidator(LexEntity $type): Validator;

    /**
     * @return array<array{0: LexEntity, 1: mixed}>
     */
    abstract public static function validTestProvider(): array;

    /**
     * @return array<array{0: LexEntity, 1: mixed, 2: string}>
     */
    abstract public static function invalidTestProvider(): array;

    #[DataProvider('validTestProvider')]
    public function testValidValue(LexEntity $type, mixed $value): void
    {
        // If the value is `null` and there's a default value, we assume the
        // expected result is the default value. Otherwise, use `null`.
        /** @var mixed $expected */
        $expected = $value ?? $type->default ?? null;

        $this->assertSame($expected, $this->getValidator($type)->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(LexEntity $type, mixed $value, string $error): void
    {
        $validator = $this->getValidator($type);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($error);

        $validator->validate($value);
    }
}
