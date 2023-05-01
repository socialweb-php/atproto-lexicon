<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexType;

interface Parser
{
    public function getParserFactory(): ParserFactory;

    public function getSchemaRepository(): SchemaRepository;

    /**
     * @param object | string $data A JSON object or string representation of
     *     the Lexicon entity to parse.
     */
    public function parse(object | string $data): LexType;

    public function setParserFactory(ParserFactory $parserFactory): void;

    public function setSchemaRepository(SchemaRepository $schemaRepository): void;
}
