<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexRecord extends LexUserType
{
    public function __construct(
        public readonly LexObject $record,
        public readonly ?string $key = null,
        ?string $description = null,
    ) {
        parent::__construct(LexUserTypeType::Record, $description);
    }
}
