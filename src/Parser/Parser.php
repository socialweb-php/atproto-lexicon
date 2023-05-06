<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexEntity;

interface Parser
{
    public function getParserFactory(): ParserFactory;

    /**
     * @param object | string $data A JSON object or string representation of
     *     the Lexicon entity to parse.
     */
    public function parse(object | string $data): LexEntity;

    public function setParserFactory(ParserFactory $parserFactory): void;
}
