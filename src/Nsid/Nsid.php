<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Nsid;

use JsonSerializable;
use Stringable;

use function array_pop;
use function explode;
use function implode;
use function is_string;

/**
 * @phpstan-type TNsid = string
 */
class Nsid implements JsonSerializable, Stringable
{
    public readonly string $nsid;
    public readonly Authority $authority;
    public readonly string $name;
    public readonly string $defId;

    public function __construct(string $nsid)
    {
        $parts = explode('.', $nsid);
        $nameAndDefId = explode('#', array_pop($parts));

        $parsedName = $nameAndDefId[0] ?? '';
        $parsedDefId = $nameAndDefId[1] ?? 'main';
        $parsedNsid = implode('.', $parts) . ".$parsedName";

        try {
            (new NsidValidator())->validate($parsedNsid);
            $parsedAuthority = new Authority(implode('.', $parts));
        } catch (InvalidNsid $exception) {
            throw new InvalidNsid("Unable to parse NSID \"$nsid\"", previous: $exception);
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
        } catch (InvalidNsid) {
            return false;
        }

        return true;
    }

    public function __toString(): string
    {
        $fragment = $this->defId !== 'main' ? '#' . $this->defId : '';

        return $this->nsid . $fragment;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
