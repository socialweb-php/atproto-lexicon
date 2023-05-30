<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Nsid;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Nsid\InvalidNsid;
use SocialWeb\Atproto\Lexicon\Nsid\NsidValidator;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function str_repeat;

class NsidValidatorTest extends TestCase
{
    #[DataProvider('validTestProvider')]
    public function testValidValue(string $value): void
    {
        $this->assertSame($value, (new NsidValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(mixed $value, string $error): void
    {
        $this->expectException(InvalidNsid::class);
        $this->expectExceptionMessage($error);

        (new NsidValidator())->validate($value);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validTestProvider(): array
    {
        return [
            ['com.example.foo'],
            ['com.example.foo.*'],
            ['com.' . str_repeat('o', 63) . '.foo'],
            ['com.example.' . str_repeat('o', 128)],
            ['com.' . str_repeat('middle.', 50) . 'foo'],
            ['a.b.c'],
            ['a0.b1.c3'],
            ['a-0.b-1.c-3'],
            ['m.xn--masekowski-d0b.pl'],
            ['one.two.three'],

            // Allows onion (Tor) NSIDs
            ['onion.expyuzz4wqqyqhjn.spec.getThing'],
            ['onion.g2zyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid.lex.deleteThing'],
        ];
    }

    /**
     * @return array<array{0: mixed, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            [1234, 'NSID must be a string'],
            [12.34, 'NSID must be a string'],
            [false, 'NSID must be a string'],
            [[], 'NSID must be a string'],
            [(object) [], 'NSID must be a string'],
            [null, 'NSID must be a string'],
            ['com.' . str_repeat('o', 64) . '.foo', 'NSID domain part cannot be longer than 63 characters'],
            ['com.example.' . str_repeat('o', 129), 'NSID name part cannot be longer than 128 characters'],
            ['com.' . str_repeat('middle.', 100) . 'foo', 'NSID cannot be longer than 382 characters'],
            ['example.com', 'NSID needs at least three parts'],
            ['com.example', 'NSID needs at least three parts'],
            ['a.0.c', 'NSID parts must start with an ASCII letter'],
            ['a.', 'NSID needs at least three parts'],
            ['.one.two.three', 'NSID parts cannot be empty'],
            ['one.two.three ', 'Invalid characters found in NSID'],
            ['one.two..three', 'NSID parts cannot be empty'],
            ['one .two.three', 'Invalid characters found in NSID'],
            [' one.two.three', 'Invalid characters found in NSID'],
            ['com.exa💩ple.thing', 'Invalid characters found in NSID'],
            ['com.atproto.feed.p@st', 'Invalid characters found in NSID'],
            ['com.atproto.feed.p_st', 'Invalid characters found in NSID'],
            ['com.atproto.feed.p*st', 'Invalid characters found in NSID'],
            ['com.atproto.feed.po#t', 'Invalid characters found in NSID'],
            ['com.atproto.feed.p!ot', 'Invalid characters found in NSID'],
            ['com.example-.foo', 'NSID parts cannot end with a hyphen'],

            // Blocks starting-with-numeric segments (differently from domains)
            ['org.4chan.lex.getThing', 'NSID parts must start with an ASCII letter'],
            ['cn.8.lex.stuff', 'NSID parts must start with an ASCII letter'],
            [
                'onion.2gzyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid.lex.deleteThing',
                'NSID parts must start with an ASCII letter',
            ],
        ];
    }
}
