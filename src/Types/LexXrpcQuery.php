<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

final class LexXrpcQuery extends LexUserType
{
    /**
     * @param array<string, LexPrimitive> $parameters
     * @param LexXrpcError[] $errors
     */
    public function __construct(
        public readonly ?array $parameters = null,
        public readonly ?LexXrpcBody $output = null,
        public readonly ?array $errors = null,
        ?string $description = null,
    ) {
        parent::__construct(LexUserTypeType::Query, $description);
    }
}
