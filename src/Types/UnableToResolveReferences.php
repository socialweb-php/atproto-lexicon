<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use RuntimeException;
use SocialWeb\Atproto\Lexicon\LexiconException;

class UnableToResolveReferences extends RuntimeException implements LexiconException
{
}
