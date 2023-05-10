<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

trait LexEntityParent
{
    private ?LexEntity $parent = null;

    public function getParent(): ?LexEntity
    {
        return $this->parent;
    }

    public function setParent(LexEntity $entity): void
    {
        $this->parent = $entity;
    }
}
