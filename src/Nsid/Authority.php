<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Nsid;

use JsonSerializable;
use Stringable;

use function array_reverse;
use function explode;
use function filter_var;
use function implode;

use const FILTER_FLAG_HOSTNAME;
use const FILTER_VALIDATE_DOMAIN;

class Authority implements JsonSerializable, Stringable
{
    public readonly string $authority;

    /**
     * @param string $authority The NSID authority portion in reverse domain
     *     name notation (i.e., "com.example.foo"). If the authority portion has
     *     already been reordered to standard domain name notation (i.e.,
     *     "foo.example.com"), pass `true` to the `$isParsed` parameter.
     * @param bool $isParsed If `true`, treat the provided `$authority` as
     *     having already been parsed into standard domain name notation order
     *     (i.e., "foo.example.com").
     */
    public function __construct(string $authority, bool $isParsed = false)
    {
        if ($isParsed) {
            $parsedAuthority = $authority;
        } else {
            $parts = explode('.', $authority);
            $parsedAuthority = implode('.', array_reverse($parts));
        }

        if (!filter_var($parsedAuthority, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new InvalidNsid("Unable to parse authority \"$authority\"");
        }

        $this->authority = $parsedAuthority;
    }

    public function __toString(): string
    {
        return $this->authority;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
