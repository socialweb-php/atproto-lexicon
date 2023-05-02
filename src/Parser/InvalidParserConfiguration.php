<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use LogicException;
use SocialWeb\Atproto\Lexicon\LexiconException;

class InvalidParserConfiguration extends LogicException implements LexiconException
{
}
