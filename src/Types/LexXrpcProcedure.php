<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-import-type TLexXrpcBody from LexXrpcBody
 * @phpstan-import-type TLexXrpcError from LexXrpcError
 * @phpstan-import-type TLexXrpcParameters from LexXrpcParameters
 * @phpstan-type TLexXrpcProcedure = object{
 *     type: 'procedure',
 *     description?: string,
 *     parameters?: TLexXrpcParameters,
 *     input?: TLexXrpcBody,
 *     output?: TLexXrpcBody,
 *     errors?: list<TLexXrpcError>,
 * }
 */
class LexXrpcProcedure implements JsonSerializable, LexUserType
{
    use LexEntityJsonSerializer;

    public readonly LexType $type;

    /**
     * @param list<LexXrpcError> $errors
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?LexXrpcParameters $parameters = null,
        public readonly ?LexXrpcBody $input = null,
        public readonly ?LexXrpcBody $output = null,
        public readonly ?array $errors = null,
    ) {
        $this->type = LexType::Procedure;
    }
}
