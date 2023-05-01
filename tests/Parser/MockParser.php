<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Parser\Parser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

class MockParser implements Parser
{
    public LexType $parsedValue;
    public int $parseCalled = 0;
    public int $setParserFactoryCalled = 0;
    public int $setSchemaRepositoryCalled = 0;

    public function __construct()
    {
        $this->parsedValue = new LexiconDoc('foo', []);
    }

    public function parse(object | string $data): LexType
    {
        $this->parseCalled++;

        return $this->parsedValue;
    }

    public function setParserFactory(ParserFactory $parserFactory): void
    {
        $this->setParserFactoryCalled++;
    }

    public function setSchemaRepository(SchemaRepository $schemaRepository): void
    {
        $this->setSchemaRepositoryCalled++;
    }
}
