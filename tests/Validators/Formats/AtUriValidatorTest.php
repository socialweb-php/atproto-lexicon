<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators\Formats;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\Formats\AtUriValidator;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function str_repeat;

class AtUriValidatorTest extends TestCase
{
    #[DataProvider('validTestProvider')]
    public function testValidValue(string $value): void
    {
        $this->assertSame($value, (new AtUriValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(mixed $value, string $error): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($error);

        (new AtUriValidator())->validate($value);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validTestProvider(): array
    {
        return [
            ['at://did:plc:asdf123'],
            ['at://user.bsky.social'],
            ['at://did:plc:asdf123/com.atproto.feed.post'],
            ['at://did:plc:asdf123/com.atproto.feed.post/record'],
            ['at://did:plc:asdf123#/frag'],
            ['at://user.bsky.social#/frag'],
            ['at://did:plc:asdf123/com.atproto.feed.post#/frag'],
            ['at://did:plc:asdf123/com.atproto.feed.post/record#/frag'],
            ['at://did:plc:asdf123/com.atproto.feed.post/' . str_repeat('o', 8149)],
        ];
    }

    /**
     * @return array<array{0: mixed, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            [1234, 'AT URI must be a string'],
            [12.34, 'AT URI must be a string'],
            [false, 'AT URI must be a string'],
            [[], 'AT URI must be a string'],
            [(object) [], 'AT URI must be a string'],
            [null, 'AT URI must be a string'],
            ['at://user.bsky.social#/foo#/bar', 'AT URI can have at most one hash mark (#)'],
            ['at://did:plc:asdf123//foo', 'AT URI cannot have a slash after the authority without a path segment'],
            [
                'at://did:plc:asdf123/com.atproto.feed.post//foo',
                'AT URI cannot have a slash after the collection unless a record key is provided',
            ],
            [
                'at://did:plc:asdf123/com.atproto.feed.post/record/',
                'AT URI path can have at most two parts and no trailing slash',
            ],
            ['at://did:plc:asdf123/com.atproto.feed.post#', 'AT URI fragment must be non-empty and start with slash'],
            ['at://did:plc:asdf123/com.atproto.feed.post#ab', 'AT URI fragment must be non-empty and start with slash'],
            [
                'at://did:plc:asdf123/com.atproto.feed.post/' . str_repeat('o', 8150),
                'AT URI cannot be longer than 8 KB',
            ],
            ['a://did:plc:asdf123', 'AT URI must use "at://" scheme'],
            ['at//did:plc:asdf123', 'AT URI must use "at://" scheme'],
            ['at:/a/did:plc:asdf123', 'AT URI must use "at://" scheme'],
            ['at:/did:plc:asdf123', 'AT URI requires at least method and authority sections'],
            ['AT://did:plc:asdf123', 'AT URI must use "at://" scheme'],
            ['http://did:plc:asdf123', 'AT URI must use "at://" scheme'],
            ['://did:plc:asdf123', 'AT URI must use "at://" scheme'],
            ['at:did:plc:asdf123', 'AT URI requires at least method and authority sections'],
            ['at:/did:plc:asdf123', 'AT URI requires at least method and authority sections'],
            ['at:///did:plc:asdf123', 'AT URI authority must be a valid handle or DID'],
            ['at://:/did:plc:asdf123', 'AT URI authority must be a valid handle or DID'],
            ['at:/ /did:plc:asdf123', 'Invalid characters found in AT URI'],
            ['at://did:plc:asdf123 ', 'Invalid characters found in AT URI'],
            ['at://did:plc:asdf123/ ', 'Invalid characters found in AT URI'],
            [' at://did:plc:asdf123', 'Invalid characters found in AT URI'],
            ['at://did:plc:asdf123/com.atproto.feed.post ', 'Invalid characters found in AT URI'],
            ['at://did:plc:asdf123/com.atproto.feed.post# ', 'AT URI fragment must be non-empty and start with slash'],
            ['at://did:plc:asdf123/com.atproto.feed.post#/ ', 'Invalid characters found in AT URI fragment'],
            ['at://did:plc:asdf123/com.atproto.feed.post#/frag ', 'Invalid characters found in AT URI fragment'],
            [
                'at://did:plc:asdf123/com.atproto.feed.post#fr ag',
                'AT URI fragment must be non-empty and start with slash',
            ],
            ['//did:plc:asdf123', 'AT URI must use "at://" scheme'],
            ['at://name', 'AT URI authority must be a valid handle or DID'],
            ['at://name.0', 'AT URI authority must be a valid handle or DID'],
            ['at://diD:plc:asdf123', 'AT URI authority must be a valid handle or DID'],
            [
                'at://did:plc:asdf123/com.atproto.feed.p@st',
                'AT URI requires valid NSID as first path segment (if provided)',
            ],
            [
                'at://did:plc:asdf123/com.atproto.feed.p$st',
                'AT URI requires valid NSID as first path segment (if provided)',
            ],
            [
                'at://did:plc:asdf123/com.atproto.feed.p%st',
                'AT URI requires valid NSID as first path segment (if provided)',
            ],
            [
                'at://did:plc:asdf123/com.atproto.feed.p&st',
                'AT URI requires valid NSID as first path segment (if provided)',
            ],
            [
                'at://did:plc:asdf123/com.atproto.feed.p()t',
                'AT URI requires valid NSID as first path segment (if provided)',
            ],
            [
                'at://did:plc:asdf123/com.atproto.feed_post',
                'AT URI requires valid NSID as first path segment (if provided)',
            ],
            [
                'at://did:plc:asdf123/-com.atproto.feed.post',
                'AT URI requires valid NSID as first path segment (if provided)',
            ],
            ['at://did:plc:asdf@123/com.atproto.feed.post', 'AT URI authority must be a valid handle or DID'],
            ['at://DID:plc:asdf123', 'AT URI authority must be a valid handle or DID'],
            ['at://user.bsky.123', 'AT URI authority must be a valid handle or DID'],
            ['at://bsky', 'AT URI authority must be a valid handle or DID'],
            ['at://did:plc:', 'AT URI authority must be a valid handle or DID'],
            ['at://did:plc:', 'AT URI authority must be a valid handle or DID'],
            ['at://frag', 'AT URI authority must be a valid handle or DID'],
        ];
    }
}
