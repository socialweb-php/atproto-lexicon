<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\Parser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function is_string;
use function json_encode;

use const JSON_UNESCAPED_SLASHES;

abstract class ParserTestCase extends TestCase
{
    /**
     * @return class-string<Parser>
     */
    abstract public function getParserClassName(): string;

    /**
     * @return array<array{value: object | string}>
     */
    abstract public static function invalidValuesProvider(): array;

    #[DataProvider('invalidValuesProvider')]
    public function testThrowsForInvalidValues(object | string $value): void
    {
        $parserClass = $this->getParserClassName();
        $parser = new $parserClass();

        $schemaRepo = new SchemaRepository(__DIR__ . '/../schemas');
        $parser->setParserFactory(new ParserFactory($schemaRepo));

        $this->expectException(UnableToParse::class);
        $this->expectExceptionMessage(
            'The input data does not contain a valid schema definition: "'
            . (is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_SLASHES)) . '"',
        );

        $parser->parse($value);
    }
}
