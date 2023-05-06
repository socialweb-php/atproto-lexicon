<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * Base type for all Lexicon types
 *
 * @phpstan-import-type TLexBytes from LexBytes
 * @phpstan-import-type TLexCidLink from LexCidLink
 * @phpstan-import-type TLexRef from LexRef
 * @phpstan-import-type TLexRefUnion from LexRefUnion
 * @phpstan-type TLexIpldType = TLexBytes | TLexCidLink
 * @phpstan-type TLexRefVariant = TLexRef | TLexRefUnion
 */
interface LexEntity
{
}
