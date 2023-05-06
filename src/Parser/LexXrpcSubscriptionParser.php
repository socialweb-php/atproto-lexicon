<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexXrpcSubscription;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcType;

class LexXrpcSubscriptionParser extends LexXrpcParser implements Parser
{
    public function parse(object | string $data): LexXrpcSubscription
    {
        /** @var LexXrpcSubscription */
        return $this->parseXrpc($data, LexXrpcType::Subscription);
    }
}
