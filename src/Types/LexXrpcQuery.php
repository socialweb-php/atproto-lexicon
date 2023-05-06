<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexXrpcBodyJson from LexXrpcBody
 * @phpstan-import-type LexXrpcErrorJson from LexXrpcError
 * @phpstan-type LexXrpcQueryJson = object{
 *     type: 'query',
 *     description?: string,
 *     parameters?: array<string, LexPrimitive>,
 *     output?: LexXrpcBodyJson,
 *     errors?: LexXrpcErrorJson[],
 * }
 */
final class LexXrpcQuery extends LexUserType
{
    /**
     * @param array<string, LexPrimitive> $parameters
     * @param LexXrpcError[] $errors
     */
    public function __construct(
        ?string $description = null,
        public readonly ?array $parameters = null,
        public readonly ?LexXrpcBody $output = null,
        public readonly ?array $errors = null,
    ) {
        parent::__construct(LexUserTypeType::Query, $description);
    }
}
