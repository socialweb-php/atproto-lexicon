<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use RuntimeException;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Parser\Parser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

class MockParser implements Parser
{
    public LexEntity $parsedValue;
    public int $parseCalled = 0;
    public int $setParserFactoryCalled = 0;

    private ?ParserFactory $parserFactory = null;

    public function __construct()
    {
        $this->parsedValue = new LexiconDoc(id: new Nsid('foo.bar.baz'));
    }

    public function getParserFactory(): ParserFactory
    {
        if ($this->parserFactory === null) {
            throw new RuntimeException('You forgot to set a parser factory');
        }

        return $this->parserFactory;
    }

    public function parse(object | string $data): LexEntity
    {
        $this->parseCalled++;

        return $this->parsedValue;
    }

    public function setParserFactory(ParserFactory $parserFactory): void
    {
        $this->parserFactory = $parserFactory;
        $this->setParserFactoryCalled++;
    }
}
