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
    /**
     * Returns the direct ancestor (e.g., a LexObject, LexXrpcBody, LexiconDoc)
     * to which this entity belongs, if it is known.
     */
    public function getParent(): ?LexEntity;

    /**
     * Sets the direct ancestor of this entity.
     */
    public function setParent(LexEntity $entity): void;
}
