<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\TestWith;
use SocialWeb\Atproto\Lexicon\Parser\Parser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\ParserNotFound;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class ParserFactoryTest extends TestCase
{
    /**
     * @param class-string<Parser> $invalidParserName
     */
    #[TestWith(['ThisClassDoesNotExist'])]
    #[TestWith([self::class])]
    public function testGetParserThrowsForUnknownParser(string $invalidParserName): void
    {
        $schemaRepository = new SchemaRepository(__DIR__ . '/../schemas');
        $parserRepository = new ParserFactory($schemaRepository);

        $this->expectException(ParserNotFound::class);
        $this->expectExceptionMessage("Unable to find parser \"$invalidParserName\"");

        $parserRepository->getParser($invalidParserName);
    }

    public function testGetParserReturnsStoredParser(): void
    {
        $parser = new MockParser();
        $schemaRepository = new SchemaRepository(__DIR__ . '/../schemas');
        $parserRepository = new ParserFactory($schemaRepository, [MockParser::class => $parser]);

        $this->assertSame($parser, $parserRepository->getParser(MockParser::class));
    }

    public function testGetParserConstructsAndStoresParser(): void
    {
        $schemaRepository = new SchemaRepository(__DIR__ . '/../schemas');
        $parserRepository = new ParserFactory($schemaRepository);

        $parser1 = $parserRepository->getParser(MockParser::class);
        $parser2 = $parserRepository->getParser(MockParser::class);

        $this->assertInstanceOf(MockParser::class, $parser1);
        $this->assertSame(1, $parser1->setParserFactoryCalled);
        $this->assertSame(1, $parser1->setSchemaRepositoryCalled);
        $this->assertSame(0, $parser1->parseCalled);
        $this->assertSame($parser1, $parser2);
    }
}