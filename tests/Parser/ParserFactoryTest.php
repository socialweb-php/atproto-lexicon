<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\TestWith;
use SocialWeb\Atproto\Lexicon\Parser\LexArrayParser;
use SocialWeb\Atproto\Lexicon\Parser\LexBlobParser;
use SocialWeb\Atproto\Lexicon\Parser\LexBooleanParser;
use SocialWeb\Atproto\Lexicon\Parser\LexBytesParser;
use SocialWeb\Atproto\Lexicon\Parser\LexCidLinkParser;
use SocialWeb\Atproto\Lexicon\Parser\LexIntegerParser;
use SocialWeb\Atproto\Lexicon\Parser\LexObjectParser;
use SocialWeb\Atproto\Lexicon\Parser\LexRecordParser;
use SocialWeb\Atproto\Lexicon\Parser\LexRefParser;
use SocialWeb\Atproto\Lexicon\Parser\LexRefUnionParser;
use SocialWeb\Atproto\Lexicon\Parser\LexStringParser;
use SocialWeb\Atproto\Lexicon\Parser\LexTokenParser;
use SocialWeb\Atproto\Lexicon\Parser\LexUnknownParser;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcProcedureParser;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcQueryParser;
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

    /**
     * @param class-string<Parser> $expectedParserClass
     */
    #[TestWith(['array', LexArrayParser::class])]
    #[TestWith(['blob', LexBlobParser::class])]
    #[TestWith(['boolean', LexBooleanParser::class])]
    #[TestWith(['bytes', LexBytesParser::class])]
    #[TestWith(['cid-link', LexCidLinkParser::class])]
    #[TestWith(['integer', LexIntegerParser::class])]
    #[TestWith(['object', LexObjectParser::class])]
    #[TestWith(['procedure', LexXrpcProcedureParser::class])]
    #[TestWith(['query', LexXrpcQueryParser::class])]
    #[TestWith(['ref', LexRefParser::class])]
    #[TestWith(['record', LexRecordParser::class])]
    #[TestWith(['string', LexStringParser::class])]
    #[TestWith(['token', LexTokenParser::class])]
    #[TestWith(['union', LexRefUnionParser::class])]
    #[TestWith(['unknown', LexUnknownParser::class])]
    public function testGetParserByTypeName(string $typeName, string $expectedParserClass): void
    {
        $schemaRepository = new SchemaRepository(__DIR__ . '/../schemas');
        $parserRepository = new ParserFactory($schemaRepository);
        $parser = $parserRepository->getParserByTypeName($typeName);

        $this->assertInstanceOf($expectedParserClass, $parser);
    }

    #[TestWith(['foobar'])]
    public function testGetParserByTypeNameThrowsForUnknownTypeName(string $typeName): void
    {
        $schemaRepository = new SchemaRepository(__DIR__ . '/../schemas');
        $parserRepository = new ParserFactory($schemaRepository);

        $this->expectException(ParserNotFound::class);
        $this->expectExceptionMessage("Unable to find parser for \"$typeName\"");

        $parserRepository->getParserByTypeName($typeName);
    }
}
