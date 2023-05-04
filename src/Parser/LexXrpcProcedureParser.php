<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexXrpcMethodType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;

final class LexXrpcProcedureParser extends LexXrpcMethodParser implements Parser
{
    public function parse(object | string $data): LexXrpcProcedure
    {
        /** @var LexXrpcProcedure */
        return $this->parseMethod($data, LexXrpcMethodType::Procedure);
    }
}
