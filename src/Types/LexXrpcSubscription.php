<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type TLexXrpcError from LexXrpcError
 * @phpstan-import-type TLexXrpcParameters from LexXrpcParameters
 * @phpstan-import-type TLexXrpcSubscriptionMessage from LexXrpcSubscriptionMessage
 * @phpstan-type TLexXrpcSubscription = object{
 *     type: 'subscription',
 *     description?: string,
 *     parameters?: TLexXrpcParameters,
 *     message?: TLexXrpcSubscriptionMessage,
 *     infos?: list<TLexXrpcError>,
 *     errors?: list<TLexXrpcError>,
 * }
 */
class LexXrpcSubscription implements LexUserType
{
    public readonly LexType $type;

    /**
     * @param list<LexXrpcError> $infos
     * @param list<LexXrpcError> $errors
     */
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?LexXrpcParameters $parameters = null,
        public readonly ?LexXrpcSubscriptionMessage $message = null,
        public readonly ?array $infos = null,
        public readonly ?array $errors = null,
    ) {
        $this->type = LexType::Subscription;
    }
}
