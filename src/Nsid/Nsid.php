<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Nsid;

use JsonSerializable;

use function array_pop;
use function array_reverse;
use function explode;
use function implode;
use function is_string;

/**
 * @phpstan-type TNsid = string
 */
class Nsid implements JsonSerializable
{
    public readonly string $nsid;
    public readonly string $authority;
    public readonly string $name;
    public readonly string $defId;

    public function __construct(string $nsid)
    {
        $parts = explode('.', $nsid);
        $nameAndDefId = explode('#', array_pop($parts));

        $parsedAuthority = implode('.', array_reverse($parts));
        $parsedName = $nameAndDefId[0] ?? '';
        $parsedDefId = $nameAndDefId[1] ?? 'main';
        $parsedNsid = implode('.', $parts) . ".$parsedName";

        if ($parsedNsid === '.' || !$parsedAuthority) {
            throw new InvalidNsid("Unable to parse NSID \"$nsid\"");
        }

        $this->nsid = $parsedNsid;
        $this->authority = $parsedAuthority;
        $this->name = $parsedName;
        $this->defId = $parsedDefId;
    }

    public static function isValid(mixed $nsid): bool
    {
        if ($nsid instanceof Nsid) {
            return true;
        }

        if (!is_string($nsid)) {
            return false;
        }

        try {
            new self($nsid);

            return true;
        } catch (InvalidNsid) {
            return false;
        }
    }

    public function jsonSerialize(): string
    {
        $fragment = $this->defId !== 'main' ? '#' . $this->defId : '';

        return $this->nsid . $fragment;
    }
}
