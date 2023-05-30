<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Nsid;

use PHPUnit\Framework\Attributes\TestWith;
use SocialWeb\Atproto\Lexicon\Nsid\Authority;
use SocialWeb\Atproto\Lexicon\Nsid\InvalidNsid;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function json_encode;
use function sprintf;

class AuthorityTest extends TestCase
{
    #[TestWith(['com.example', 'example.com'])]
    #[TestWith(['io.social', 'social.io'])]
    #[TestWith(['net.users.bob', 'bob.users.net'])]
    #[TestWith(['com.example.foo', 'foo.example.com'])]
    #[TestWith(['foo.example.com', 'foo.example.com', true])]
    public function testAuthorityParsing(
        string $preParsedAuthority,
        string $expectedParsedAuthority,
        bool $isParsed = false,
    ): void {
        $authority = new Authority($preParsedAuthority, $isParsed);

        $this->assertSame($expectedParsedAuthority, $authority->authority);
        $this->assertSame($expectedParsedAuthority, (string) $authority);
        $this->assertSame(sprintf('"%s"', $expectedParsedAuthority), json_encode($authority));
    }

    public function testAuthorityThrows(): void
    {
        $this->expectException(InvalidNsid::class);
        $this->expectExceptionMessage('Unable to parse authority "...foo"');

        new Authority('...foo');
    }
}
