<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators\Formats;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\Formats\NsidValidator;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
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
        $this->expectException(InvalidValue::class);
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
            [1234, 'Value must be a valid NSID'],
            [12.34, 'Value must be a valid NSID'],
            [false, 'Value must be a valid NSID'],
            [[], 'Value must be a valid NSID'],
            [(object) [], 'Value must be a valid NSID'],
            [null, 'Value must be a valid NSID'],
            ['com.' . str_repeat('o', 64) . '.foo', 'Value must be a valid NSID'],
            ['com.example.' . str_repeat('o', 129), 'Value must be a valid NSID'],
            ['com.' . str_repeat('middle.', 100) . 'foo', 'Value must be a valid NSID'],
            ['example.com', 'Value must be a valid NSID'],
            ['com.example', 'Value must be a valid NSID'],
            ['a.0.c', 'Value must be a valid NSID'],
            ['a.', 'Value must be a valid NSID'],
            ['.one.two.three', 'Value must be a valid NSID'],
            ['one.two.three ', 'Value must be a valid NSID'],
            ['one.two..three', 'Value must be a valid NSID'],
            ['one .two.three', 'Value must be a valid NSID'],
            [' one.two.three', 'Value must be a valid NSID'],
            ['com.exa💩ple.thing', 'Value must be a valid NSID'],
            ['com.atproto.feed.p@st', 'Value must be a valid NSID'],
            ['com.atproto.feed.p_st', 'Value must be a valid NSID'],
            ['com.atproto.feed.p*st', 'Value must be a valid NSID'],
            ['com.atproto.feed.po#t', 'Value must be a valid NSID'],
            ['com.atproto.feed.p!ot', 'Value must be a valid NSID'],
            ['com.example-.foo', 'Value must be a valid NSID'],

            // Blocks starting-with-numeric segments (differently from domains)
            ['org.4chan.lex.getThing', 'Value must be a valid NSID'],
            ['cn.8.lex.stuff', 'Value must be a valid NSID'],
            [
                'onion.2gzyxa5ihm7nsggfxnu52rck2vv4rvmdlkiu3zzui5du4xyclen53wid.lex.deleteThing',
                'Value must be a valid NSID',
            ],
        ];
    }
}
