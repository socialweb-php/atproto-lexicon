<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators\Formats;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\Formats\DidValidator;
use SocialWeb\Atproto\Lexicon\Validators\Formats\InvalidDid;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function str_repeat;

class DidValidatorTest extends TestCase
{
    #[DataProvider('validTestProvider')]
    public function testValidValue(string $value): void
    {
        $this->assertSame($value, (new DidValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(mixed $value, string $error): void
    {
        $this->expectException(InvalidDid::class);
        $this->expectExceptionMessage($error);

        (new DidValidator())->validate($value);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validTestProvider(): array
    {
        return [
            ['did:method:val'],
            ['did:method:VAL'],
            ['did:method:val123'],
            ['did:method:123'],
            ['did:method:val-two'],
            ['did:method:val_two'],
            ['did:method:val.two'],
            ['did:method:val:two'],
            ['did:method:val%BB'],
            ['did:method:' . str_repeat('v', 240)],
            ['did:m:v'],
            ['did:method::::val'],
            ['did:method:-'],
            ['did:method:-:_:.:%ab'],
            ['did:method:.'],
            ['did:method:_'],
            ['did:method::.'],
            ['did:onion:2gzyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid'],
            ['did:example:123456789abcdefghi'],
            ['did:plc:7iza6de2dwap2sbkpav7c6c6'],
            ['did:web:example.com'],
            ['did:key:zQ3shZc2QzApp2oymGvQbzP8eKheVshBHbU4ZYjeXqwSKEn6N'],
            ['did:ethr:0xb9c5714089478a327f09197987f16f9e5d936e8a'],
            ['did:plc:7iza6de2dwap2sbkpav7c6c6' . str_repeat('a', 8160)],
        ];
    }

    /**
     * @return array<array{0: mixed, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            [1234, 'DID must be a string'],
            [12.34, 'DID must be a string'],
            [false, 'DID must be a string'],
            [[], 'DID must be a string'],
            [(object) [], 'DID must be a string'],
            [null, 'DID must be a string'],
            ['did', 'DID requires a prefix, method, and method-specific content'],
            ['didmethodval', 'DID requires a prefix, method, and method-specific content'],
            ['method:did:val', 'DID requires "did:" prefix'],
            ['did:method:', 'DID cannot end with ":" or "%" characters'],
            ['didmethod:val', 'DID requires a prefix, method, and method-specific content'],
            ['did:methodval', 'DID requires a prefix, method, and method-specific content'],
            [':did:method:val', 'DID requires "did:" prefix'],
            ['did.method.val', 'DID requires a prefix, method, and method-specific content'],
            ['did:method:val:', 'DID cannot end with ":" or "%" characters'],
            ['did:method:val%', 'DID cannot end with ":" or "%" characters'],
            ['DID:method:val', 'DID requires "did:" prefix'],
            ['did:METHOD:val', 'DID method must use only lower-case letters'],
            ['did:m123:val', 'DID method must use only lower-case letters'],
            ['did:method:val/two', 'Invalid characters found in DID'],
            ['did:method:val?two', 'Invalid characters found in DID'],
            ['did:method:val#two', 'Invalid characters found in DID'],
            ['did:plc:7iza6de2dwap2sbkpav7c6c6' . str_repeat('a', 8161), 'DID cannot be longer than 8 KB'],
        ];
    }
}
