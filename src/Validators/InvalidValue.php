<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

use RuntimeException;
use SocialWeb\Atproto\Lexicon\LexiconException;

class InvalidValue extends RuntimeException implements LexiconException
{
}
