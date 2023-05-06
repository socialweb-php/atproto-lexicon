<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type TLexXrpcBody from LexXrpcBody
 * @phpstan-import-type TLexXrpcError from LexXrpcError
 * @phpstan-type TLexXrpcProcedure = object{
 *     type: 'procedure',
 *     description?: string,
 *     parameters?: array<string, LexPrimitive>,
 *     input?: TLexXrpcBody,
 *     output?: TLexXrpcBody,
 *     errors?: TLexXrpcError[],
 * }
 */
class LexXrpcProcedure implements LexUserType
{
    public readonly LexType $type;

    /**
     * @param array<string, LexPrimitive> $parameters
     * @param LexXrpcError[] $errors
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?array $parameters = null,
        public readonly ?LexXrpcBody $input = null,
        public readonly ?LexXrpcBody $output = null,
        public readonly ?array $errors = null,
    ) {
        $this->type = LexType::Procedure;
    }
}
