<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use RuntimeException;
use SocialWeb\Atproto\Lexicon\LexiconException;

class SchemaNotFound extends RuntimeException implements LexiconException
{
}
