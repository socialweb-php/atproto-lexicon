<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexBooleanJson from LexBoolean
 * @phpstan-import-type LexIntegerJson from LexInteger
 * @phpstan-import-type LexStringJson from LexString
 * @phpstan-import-type LexUnknownJson from LexUnknown
 * @phpstan-type LexPrimitiveJson = LexBooleanJson | LexIntegerJson | LexStringJson | LexUnknownJson
 */
interface LexPrimitive extends LexEntity
{
}
