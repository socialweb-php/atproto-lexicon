<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcType;

class LexXrpcQueryParser extends LexXrpcMethodParser implements Parser
{
    public function parse(object | string $data): LexXrpcQuery
    {
        /** @var LexXrpcQuery */
        return $this->parseMethod($data, LexXrpcType::Query);
    }
}
