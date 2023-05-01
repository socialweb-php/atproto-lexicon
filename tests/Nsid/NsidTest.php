<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Nsid;

use PHPUnit\Framework\Attributes\TestWith;
use SocialWeb\Atproto\Lexicon\Nsid\InvalidNsid;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class NsidTest extends TestCase
{
    #[TestWith(['com.example.status', 'com.example.status', 'example.com', 'status', 'main'])]
    #[TestWith(['io.social.getFeed', 'io.social.getFeed', 'social.io', 'getFeed', 'main'])]
    #[TestWith(['net.users.bob.ping', 'net.users.bob.ping', 'bob.users.net', 'ping', 'main'])]
    #[TestWith(['com.example.foo#bar', 'com.example.foo', 'example.com', 'foo', 'bar'])]
    public function testNsidParsing(
        string $preParsedNsid,
        string $expectedParsedNsid,
        string $expectedParsedAuthority,
        string $expectedParsedName,
        ?string $expectedParsedDefId,
    ): void {
        $nsid = new Nsid($preParsedNsid);

        $this->assertSame($expectedParsedNsid, $nsid->nsid);
        $this->assertSame($expectedParsedAuthority, $nsid->authority);
        $this->assertSame($expectedParsedName, $nsid->name);
        $this->assertSame($expectedParsedDefId, $nsid->defId);
    }

    #[TestWith(['foobar'])]
    #[TestWith(['foobar#baz'])]
    #[TestWith(['#qux'])]
    public function testNsidFailures(string $nsidShouldFail): void
    {
        $this->expectException(InvalidNsid::class);
        $this->expectExceptionMessage("Unable to parse NSID \"$nsidShouldFail\"");

        new Nsid($nsidShouldFail);
    }

    #[TestWith(['com.example.status', true])]
    #[TestWith(['io.social.getFeed', true])]
    #[TestWith(['net.users.bob.ping', true])]
    #[TestWith(['com.example.foo#bar', true])]
    #[TestWith(['foobar', false])]
    #[TestWith(['foobar#baz', false])]
    #[TestWith(['#qux', false])]
    #[TestWith([null, false])]
    #[TestWith([true, false])]
    #[TestWith([123, false])]
    #[TestWith([12.3, false])]
    #[TestWith([['abc', '123'], false])]
    #[TestWith([new Nsid('com.example.getProfile'), true])]
    public function testIsValid(mixed $nsid, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, Nsid::isValid($nsid));
    }
}
