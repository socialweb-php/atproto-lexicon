<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexXrpcBodyJson from LexXrpcBody
 * @phpstan-import-type LexXrpcErrorJson from LexXrpcError
 * @phpstan-type LexXrpcProcedureJson = object{
 *     type: 'procedure',
 *     description?: string,
 *     parameters?: array<string, LexPrimitive>,
 *     input?: LexXrpcBodyJson,
 *     output?: LexXrpcBodyJson,
 *     errors?: LexXrpcErrorJson[],
 * }
 */
final class LexXrpcProcedure extends LexUserType
{
    /**
     * @param array<string, LexPrimitive> $parameters
     * @param LexXrpcError[] $errors
     */
    public function __construct(
        ?string $description = null,
        public readonly ?array $parameters = null,
        public readonly ?LexXrpcBody $input = null,
        public readonly ?LexXrpcBody $output = null,
        public readonly ?array $errors = null,
    ) {
        parent::__construct(LexUserTypeType::Procedure, $description);
    }
}
