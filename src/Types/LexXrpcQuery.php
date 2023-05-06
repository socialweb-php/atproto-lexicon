<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type TLexXrpcBody from LexXrpcBody
 * @phpstan-import-type TLexXrpcError from LexXrpcError
 * @phpstan-type TLexXrpcQuery = object{
 *     type: 'query',
 *     description?: string,
 *     parameters?: array<string, LexPrimitive>,
 *     output?: TLexXrpcBody,
 *     errors?: list<TLexXrpcError>,
 * }
 */
class LexXrpcQuery implements LexUserType
{
    public readonly LexType $type;

    /**
     * @param array<string, LexPrimitive> $parameters
     * @param list<LexXrpcError> $errors
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?array $parameters = null,
        public readonly ?LexXrpcBody $output = null,
        public readonly ?array $errors = null,
    ) {
        $this->type = LexType::Query;
    }
}
