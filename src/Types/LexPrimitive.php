<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type TLexBoolean from LexBoolean
 * @phpstan-import-type TLexInteger from LexInteger
 * @phpstan-import-type TLexString from LexString
 * @phpstan-import-type TLexUnknown from LexUnknown
 * @phpstan-type TLexPrimitive = TLexBoolean | TLexInteger | TLexString | TLexUnknown
 */
interface LexPrimitive extends LexEntity
{
}
