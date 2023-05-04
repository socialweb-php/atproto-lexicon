<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexXrpcMethodType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;

final class LexXrpcQueryParser extends LexXrpcMethodParser implements Parser
{
    public function parse(object | string $data): LexXrpcQuery
    {
        /** @var LexXrpcQuery */
        return $this->parseMethod($data, LexXrpcMethodType::Query);
    }
}
