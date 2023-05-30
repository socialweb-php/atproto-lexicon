<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators\Formats;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\Formats\HandleValidator;
use SocialWeb\Atproto\Lexicon\Validators\Formats\InvalidHandle;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function str_repeat;

class HandleValidatorTest extends TestCase
{
    #[DataProvider('validTestProvider')]
    public function testValidValue(string $value): void
    {
        $this->assertSame($value, (new HandleValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(string $value, string $error): void
    {
        $this->expectException(InvalidHandle::class);
        $this->expectExceptionMessage($error);

        (new HandleValidator())->validate($value);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validTestProvider(): array
    {
        return [
            ['A.ISI.EDU'],
            ['XX.LCS.MIT.EDU'],
            ['SRI-NIC.ARPA'],
            ['john.test'],
            ['jan.test'],
            ['a234567890123456789.test'],
            ['john2.test'],
            ['john-john.test'],
            ['john.bsky.app'],
            ['jo.hn'],
            ['a.co'],
            ['a.org'],
            ['joh.n'],
            ['j0.h0'],
            ['shoooort' . str_repeat('.loooooooooooooooooooooooooong', 8) . '.test'],
            ['short.' . str_repeat('o', 63) . '.test'],
            ['jaymome-johnber123456.test'],
            ['jay.mome-johnber123456.test'],
            ['john.test.bsky.app'],
            ['john.t'],

            // Allows .local and .arpa handles
            ['laptop.local'],
            ['laptop.arpa'],

            // Allows punycode handles
            ['xn--ls8h.test'],
            ['xn--bcher-kva.tld'],

            // Allows onion (Tor) handles
            ['expyuzz4wqqyqhjn.onion'],
            ['friend.expyuzz4wqqyqhjn.onion'],
            ['g2zyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid.onion'],
            ['friend.g2zyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid.onion'],
            ['friend.g2zyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid.onion'],
            ['2gzyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid.onion'],
            ['friend.2gzyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid.onion'],

            // Correctly validates corner cases (modern vs. old RFCs)
            ['12345.test'],
            ['8.cn'],
            ['4chan.org'],
            ['4chan.o-g'],
            ['blah.4chan.org'],
            ['thing.a01'],
            ['120.0.0.1.com'],
            ['0john.test'],
            ['9sta--ck.com'],
            ['99stack.com'],
            ['0ohn.test'],
            ['john.t--t'],
            ['thing.0aa.thing'],

            // Is consistent with examples from stackoverflow
            ['stack.com'],
            ['sta-ck.com'],
            ['sta---ck.com'],
            ['sta--ck9.com'],
            ['stack99.com'],
            ['sta99ck.com'],
            ['google.com.uk'],
            ['google.co.in'],
            ['google.com'],
            ['maselkowski.pl'],
            ['m.maselkowski.pl'],
            ['xn--masekowski-d0b.pl'],
            ['xn--fiqa61au8b7zsevnm8ak20mc4a87e.xn--fiqs8s'],
            ['xn--stackoverflow.com'],
            ['stackoverflow.xn--com'],
            ['stackoverflow.co.uk'],
            ['xn--masekowski-d0b.pl'],
            ['xn--fiqa61au8b7zsevnm8ak20mc4a87e.xn--fiqs8s'],
        ];
    }

    /**
     * @return array<array{0: string, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            ['did:thing.test', 'Invalid characters found in handle'],
            ['did:thing', 'Invalid characters found in handle'],
            ['john-.test', 'Handle parts cannot start or end with hyphens'],
            ['john.0', 'A handle\'s final component (TLD) must start with an ASCII letter'],
            ['john.-', 'Handle parts cannot start or end with hyphens'],
            ['short.' . str_repeat('o', 64) . '.test', 'A handle part cannot be longer than 63 characters'],
            [
                'short' . str_repeat('.loooooooooooooooooooooooong', 10) . '.test',
                'A handle cannot be longer than 253 characters',
            ],
            [
                'shooooort' . str_repeat('.loooooooooooooooooooooooooong', 8) . '.test',
                'A handle cannot be longer than 253 characters',
            ],
            ['xn--bcher-.tld', 'Handle parts cannot start or end with hyphens'],
            ['john..test', 'Handle parts cannot be empty'],
            ['jo_hn.test', 'Invalid characters found in handle'],
            ['-john.test', 'Handle parts cannot start or end with hyphens'],
            ['.john.test', 'Handle parts cannot be empty'],
            ['jo!hn.test', 'Invalid characters found in handle'],
            ['jo%hn.test', 'Invalid characters found in handle'],
            ['jo&hn.test', 'Invalid characters found in handle'],
            ['jo@hn.test', 'Invalid characters found in handle'],
            ['jo*hn.test', 'Invalid characters found in handle'],
            ['jo|hn.test', 'Invalid characters found in handle'],
            ['jo:hn.test', 'Invalid characters found in handle'],
            ['jo/hn.test', 'Invalid characters found in handle'],
            ['john💩.test', 'Invalid characters found in handle'],
            ['bücher.test', 'Invalid characters found in handle'],
            ['john .test', 'Invalid characters found in handle'],
            ['john.test.', 'Handle parts cannot be empty'],
            ['john', 'A handle domain requires at least 2 parts'],
            ['john.', 'Handle parts cannot be empty'],
            ['.john', 'Handle parts cannot be empty'],
            ['john.test.', 'Handle parts cannot be empty'],
            ['.john.test', 'Handle parts cannot be empty'],
            [' john.test', 'Invalid characters found in handle'],
            ['john.test ', 'Invalid characters found in handle'],
            ['joh-.test', 'Handle parts cannot start or end with hyphens'],
            ['john.-est', 'Handle parts cannot start or end with hyphens'],
            ['john.tes-', 'Handle parts cannot start or end with hyphens'],

            // Throws on "dotless" TLD handles
            ['org', 'A handle domain requires at least 2 parts'],
            ['ai', 'A handle domain requires at least 2 parts'],
            ['gg', 'A handle domain requires at least 2 parts'],
            ['io', 'A handle domain requires at least 2 parts'],

            ['cn.8', 'A handle\'s final component (TLD) must start with an ASCII letter'],
            ['thing.0aa', 'A handle\'s final component (TLD) must start with an ASCII letter'],

            // Does not allow IP addresses as handles
            ['127.0.0.1', 'A handle\'s final component (TLD) must start with an ASCII letter'],
            ['192.168.0.142', 'A handle\'s final component (TLD) must start with an ASCII letter'],
            ['fe80::7325:8a97:c100:94b', 'Invalid characters found in handle'],
            ['2600:3c03::f03c:9100:feb0:af1f', 'Invalid characters found in handle'],

            ['-notvalid.at-all', 'Handle parts cannot start or end with hyphens'],
            ['-thing.com', 'Handle parts cannot start or end with hyphens'],
            ['www.masełkowski.pl.com', 'Invalid characters found in handle'],
        ];
    }
}
