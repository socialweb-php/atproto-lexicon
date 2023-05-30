<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexStringFormat;
use SocialWeb\Atproto\Lexicon\Validators\LexStringValidator;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function assert;

class LexStringValidatorTest extends ValidatorTestCase
{
    protected function getValidator(LexEntity $type): Validator
    {
        assert($type instanceof LexString);

        return new LexStringValidator($type);
    }

    /**
     * @return array<array{0: LexString, 1: mixed}>
     */
    public static function validTestProvider(): array
    {
        return [
            [new LexString(), 'foo'],
            [new LexString(default: 'bar'), null],
            [new LexString(default: 'baz'), 'qux'],
            [new LexString(const: 'qux'), 'qux'],
            [new LexString(enum: ['a', 'b', 'c']), 'b'],
            [new LexString(maxLength: 6), 'foobar'],
            [new LexString(minLength: 3), 'baz'],
            [new LexString(maxGraphemes: 5), '1234👨‍👩‍👧‍👦'],
            [new LexString(minGraphemes: 2), '👨‍👩‍👧‍👦👩‍👩‍👧‍👦'],
            [new LexString(format: LexStringFormat::AtIdentifier), 'did:plc:12345678abcdefghijklmnop'],
            [new LexString(format: LexStringFormat::AtUri), 'at://user.bsky.social'],
            [new LexString(format: LexStringFormat::DateTime), '2019-07-09T15:03:36.000+00:00'],
            [new LexString(format: LexStringFormat::Did), 'did:plc:7iza6de2dwap2sbkpav7c6c6'],
            [new LexString(format: LexStringFormat::Handle), 'john.test.bsky.app'],
            [new LexString(format: LexStringFormat::Nsid), 'com.example.foo'],
            [new LexString(format: LexStringFormat::Uri), 'foo://bar'],
        ];
    }

    /**
     * @return array<array{0: LexString, 1: mixed, 2: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            [new LexString(), 1234, 'Value must be a string'],
            [new LexString(), 12.34, 'Value must be a string'],
            [new LexString(), true, 'Value must be a string'],
            [new LexString(), null, 'Value must be a string'],
            [new LexString(), [], 'Value must be a string'],
            [new LexString(), (object) [], 'Value must be a string'],
            [new LexString(default: 'bar'), 1234, 'Value must be a string'],
            [new LexString(default: 'bar'), 12.34, 'Value must be a string'],
            [new LexString(default: 'bar'), true, 'Value must be a string'],
            [new LexString(default: 'bar'), [], 'Value must be a string'],
            [new LexString(default: 'bar'), (object) [], 'Value must be a string'],
            [new LexString(const: 'foo'), 'qux', 'Value must be foo'],
            [new LexString(enum: ['a', 'b', 'c']), 'd', 'Value must be one of (a|b|c)'],
            [new LexString(maxLength: 8), 'foobarbaz', 'Value must not be longer than 8 characters'],
            [new LexString(minLength: 4), 'qux', 'Value must not be shorter than 4 characters'],
            [new LexString(maxGraphemes: 4), '1234👨‍👩‍👧‍👦', 'Value must not be longer than 4 graphemes'],
            [new LexString(minGraphemes: 2), '👨‍👩‍👧‍👦', 'Value must not be shorter than 2 graphemes'],
            [new LexString(format: LexStringFormat::AtIdentifier), 'bad id', 'Value must be a valid handle or DID'],
            [new LexString(format: LexStringFormat::AtUri), 'http://did:plc:asdf123', 'AT URI must use "at://" scheme'],
            [
                new LexString(format: LexStringFormat::DateTime),
                '2020-12-04',
                'Value must be an ISO 8601 formatted datetime string',
            ],
            [new LexString(format: LexStringFormat::Did), 'method:did:val', 'DID requires "did:" prefix'],
            [new LexString(format: LexStringFormat::Handle), 'did:thing.test', 'Invalid characters found in handle'],
            [new LexString(format: LexStringFormat::Nsid), 'example.com', 'NSID needs at least three parts'],
            [new LexString(format: LexStringFormat::Uri), 'foobar', 'Value must be a URI'],
        ];
    }
}
