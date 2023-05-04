<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use RuntimeException;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
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

    private ?ParserFactory $parserFactory = null;
    private ?SchemaRepository $schemaRepository = null;

    public function __construct()
    {
        $this->parsedValue = new LexiconDoc(new Nsid('foo.bar.baz'), []);
    }

    public function getParserFactory(): ParserFactory
    {
        if ($this->parserFactory === null) {
            throw new RuntimeException('You forgot to set a parser factory');
        }

        return $this->parserFactory;
    }

    public function getSchemaRepository(): SchemaRepository
    {
        if ($this->schemaRepository === null) {
            throw new RuntimeException('You forgot to set a schema repository');
        }

        return $this->schemaRepository;
    }

    public function parse(object | string $data): LexType
    {
        $this->parseCalled++;

        return $this->parsedValue;
    }

    public function setParserFactory(ParserFactory $parserFactory): void
    {
        $this->parserFactory = $parserFactory;
        $this->setParserFactoryCalled++;
    }

    public function setSchemaRepository(SchemaRepository $schemaRepository): void
    {
        $this->schemaRepository = $schemaRepository;
        $this->setSchemaRepositoryCalled++;
    }
}
