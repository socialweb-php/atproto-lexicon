<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\TestWith;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexiconParser;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function sprintf;

class LexiconParserTest extends TestCase
{
    private LexiconParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $this->parser = new LexiconParser(new DefaultParserFactory($schemaRepo));
    }

    #[TestWith([''])]
    #[TestWith(['"foobar"'])]
    #[TestWith(['null'])]
    #[TestWith(['1234'])]
    #[TestWith(['123.456'])]
    #[TestWith(['{}'])]
    #[TestWith(['{"foo":"bar"}'])]
    #[TestWith(['[]'])]
    #[TestWith(['[1,2,3]'])]
    public function testThrowsValidationErrorWhenStringDoesNotParseToObject(string $json): void
    {
        $this->expectException(UnableToParse::class);
        $this->expectExceptionMessage(sprintf(
            'The input data does not contain a valid schema definition: %s',
            $json,
        ));

        $this->parser->parse($json);
    }

    /**
     * @param class-string $expectedType
     */
    #[TestWith(['{"type":"object"}', LexObject::class])]
    #[TestWith(['{"type":"string"}', LexString::class])]
    #[TestWith(['{"type":"ref","ref":"com.example.foobar"}', LexRef::class])]
    #[TestWith(['{"lexicon":1,"id":"com.example.helloThere"}', LexiconDoc::class])]
    public function testReturnsParsedDataType(string $json, string $expectedType): void
    {
        $parsedData = $this->parser->parse($json);

        $this->assertInstanceOf($expectedType, $parsedData);
    }
}
