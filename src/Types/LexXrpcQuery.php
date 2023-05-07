<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-import-type TLexXrpcBody from LexXrpcBody
 * @phpstan-import-type TLexXrpcError from LexXrpcError
 * @phpstan-import-type TLexXrpcParameters from LexXrpcParameters
 * @phpstan-type TLexXrpcQuery = object{
 *     type: 'query',
 *     description?: string,
 *     parameters?: TLexXrpcParameters,
 *     output?: TLexXrpcBody,
 *     errors?: list<TLexXrpcError>,
 * }
 */
class LexXrpcQuery implements JsonSerializable, LexUserType
{
    use LexEntityJsonSerializer;

    public readonly LexType $type;

    /**
     * @param list<LexXrpcError> $errors
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?LexXrpcParameters $parameters = null,
        public readonly ?LexXrpcBody $output = null,
        public readonly ?array $errors = null,
    ) {
        $this->type = LexType::Query;
    }
}
