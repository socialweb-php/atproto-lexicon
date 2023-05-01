<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Nsid;

use RuntimeException;
use SocialWeb\Atproto\Lexicon\LexiconException;

class InvalidNsid extends RuntimeException implements LexiconException
{
}
