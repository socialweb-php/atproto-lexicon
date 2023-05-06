<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcType;

class LexXrpcProcedureParser extends LexXrpcParser implements Parser
{
    public function parse(object | string $data): LexXrpcProcedure
    {
        /** @var LexXrpcProcedure */
        return $this->parseXrpc($data, LexXrpcType::Procedure);
    }
}
