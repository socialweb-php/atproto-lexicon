<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators;

use ArrayObject;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Validators\LexBlobValidator;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function assert;

class LexBlobValidatorTest extends ValidatorTestCase
{
    protected function getValidator(LexEntity $type): Validator
    {
        assert($type instanceof LexBlob);

        return new LexBlobValidator($type);
    }

    /**
     * @return array<array{0: LexBlob, 1: mixed}>
     */
    public static function validTestProvider(): array
    {
        return [
            [
                new LexBlob(),
                [
                    '$type' => 'blob',
                    'ref' => ['foo' => 'bar'],
                    'mimeType' => 'image/jpeg',
                    'size' => 245_123,
                ],
            ],
            [
                new LexBlob(),
                (object) [
                    '$type' => 'blob',
                    'ref' => (object) ['foo' => 'bar'],
                    'mimeType' => 'image/png',
                    'size' => 245_123.1234,
                ],
            ],
            [
                new LexBlob(),
                new ArrayObject([
                    '$type' => 'blob',
                    'ref' => new ArrayObject(['foo' => 'bar']),
                    'mimeType' => 'image/png',
                    'size' => 245_123.1234,
                ]),
            ],
            [
                new LexBlob(accept: ['image/jpeg', 'image/png'], maxSize: 1024 * 1024),
                [
                    '$type' => 'blob',
                    'ref' => ['foo' => 'bar'],
                    'mimeType' => 'image/jpeg',
                    'size' => 1024 * 500,
                ],
            ],
            [
                new LexBlob(),
                [
                    'cid' => 'foobar',
                    'mimeType' => 'application/ld+json',
                ],
            ],
            [
                new LexBlob(),
                (object) [
                    'cid' => 'foobar',
                    'mimeType' => 'application/vnd.api+json; charset=UTF-8',
                ],
            ],
            [
                new LexBlob(),
                new ArrayObject([
                    'cid' => 'foobar',
                    'mimeType' => 'application/vnd.oasis.opendocument.text',
                ]),
            ],
        ];
    }

    /**
     * @return array<array{0: LexBlob, 1: mixed, 2: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            'value is int' => [new LexBlob(), 1234, 'Value must be a blob ref'],
            'value is float' => [new LexBlob(), 12.34, 'Value must be a blob ref'],
            'value is bool' => [new LexBlob(), true, 'Value must be a blob ref'],
            'value is null' => [new LexBlob(), null, 'Value must be a blob ref'],
            'value is empty array' => [new LexBlob(), [], 'Value must be a blob ref'],
            'value is empty object' => [new LexBlob(), (object) [], 'Value must be a blob ref'],
            'value is string' => [new LexBlob(), 'foobar', 'Value must be a blob ref'],
            'value is list' => [
                new LexBlob(),
                ['blob', ['foo' => 'bar'], 'image/jpeg', 245_123],
                'Value must be a blob ref',
            ],
            'value is object of list' => [
                new LexBlob(),
                (object) ['blob', ['foo' => 'bar'], 'image/jpeg', 245_123],
                'Value must be a blob ref',
            ],
            'value is traversable of list' => [
                new LexBlob(),
                new ArrayObject(['blob', ['foo' => 'bar'], 'image/jpeg', 245_123]),
                'Value must be a blob ref',
            ],
            'value has invalid type' => [
                new LexBlob(),
                [
                    '$type' => 'foo',
                    'ref' => ['foo' => 'bar'],
                    'mimeType' => 'image/jpeg',
                    'size' => 245_123,
                ],
                'Value must be a blob ref',
            ],
            'value has invalid ref' => [
                new LexBlob(),
                [
                    '$type' => 'blob',
                    'ref' => 'foo',
                    'mimeType' => 'image/jpeg',
                    'size' => 245_123,
                ],
                'Value must be a blob ref',
            ],
            'value has ref list' => [
                new LexBlob(),
                [
                    '$type' => 'blob',
                    'ref' => ['foo'],
                    'mimeType' => 'image/jpeg',
                    'size' => 245_123,
                ],
                'Value must be a blob ref',
            ],
            'value has ref object of list' => [
                new LexBlob(),
                [
                    '$type' => 'blob',
                    'ref' => (object) ['foo'],
                    'mimeType' => 'image/jpeg',
                    'size' => 245_123,
                ],
                'Value must be a blob ref',
            ],
            'value has string for size' => [
                new LexBlob(),
                [
                    '$type' => 'blob',
                    'ref' => ['foo' => 'bar'],
                    'mimeType' => 'image/jpeg',
                    'size' => 'foo bar',
                ],
                'Value must be a blob ref',
            ],
            'value has size greater than max size' => [
                new LexBlob(maxSize: 1024 * 1024),
                [
                    '$type' => 'blob',
                    'ref' => ['foo' => 'bar'],
                    'mimeType' => 'image/jpeg',
                    'size' => 1024 * 1024 * 1.5,
                ],
                'Value must be a blob ref',
            ],
            'value has invalid mime type' => [
                new LexBlob(),
                [
                    '$type' => 'blob',
                    'ref' => ['foo' => 'bar'],
                    'mimeType' => 'jpeg',
                    'size' => 245_123,
                ],
                'Value must be a blob ref',
            ],
            'value has int mime type' => [
                new LexBlob(),
                [
                    '$type' => 'blob',
                    'ref' => ['foo' => 'bar'],
                    'mimeType' => 1234,
                    'size' => 245_123,
                ],
                'Value must be a blob ref',
            ],
            'value has wrong mime type' => [
                new LexBlob(accept: ['image/jpeg', 'image/png']),
                [
                    '$type' => 'blob',
                    'ref' => ['foo' => 'bar'],
                    'mimeType' => 'image/gif',
                    'size' => 245_123,
                ],
                'Value must be a blob ref',
            ],
            'value has int for cid' => [
                new LexBlob(),
                [
                    'cid' => 1234,
                    'mimeType' => 'image/jpeg',
                ],
                'Value must be a blob ref',
            ],
        ];
    }
}
