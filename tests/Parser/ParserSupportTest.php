<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Parser\InvalidParserConfiguration;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\ParserSupport;
use SocialWeb\Atproto\Lexicon\Parser\SchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\UnableToParse;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function assert;

class ParserSupportTest extends TestCase
{
    use ParserSupport;

    public function testGetParserFactoryThrowsException(): void
    {
        $this->expectException(InvalidParserConfiguration::class);
        $this->expectExceptionMessage('Please configure this parser with a parser factory');

        $this->getParserFactory();
    }

    public function testGetSchemaRepositoryThrowsException(): void
    {
        $this->expectException(InvalidParserConfiguration::class);
        $this->expectExceptionMessage('Please configure this parser with a schema repository');

        $this->getSchemaRepository();
    }

    public function testSetParserFactory(): void
    {
        $schemaRepository = new SchemaRepository(__DIR__ . '/../schemas');
        $parserFactory = new ParserFactory($schemaRepository);

        $this->setParserFactory($parserFactory);

        $this->assertSame($parserFactory, $this->getParserFactory());
    }

    public function testSetSchemaRepository(): void
    {
        $schemaRepository = new SchemaRepository(__DIR__ . '/../schemas');

        $this->setSchemaRepository($schemaRepository);

        $this->assertSame($schemaRepository, $this->getSchemaRepository());
    }

    public function testValidateThrowsForInvalidJson(): void
    {
        $this->expectException(UnableToParse::class);
        $this->expectExceptionMessage('The input data does not contain a valid schema definition: "foo bar"');

        $this->validate('foo bar', fn (): bool => true);
    }

    public function testValidateThrowsForInvalidObject(): void
    {
        $this->expectException(UnableToParse::class);
        $this->expectExceptionMessage('The input data does not contain a valid schema definition: "{"foo":"bar"}"');

        $this->validate((object) ['foo' => 'bar'], fn (object $data): bool => isset($data->baz));
    }

    public function testValidateWithJsonObject(): void
    {
        $json = '{"foo":"bar"}';
        $data = $this->validate($json, fn (object $data): bool => isset($data->foo));

        $this->assertObjectHasProperty('foo', $data);

        // Using an assertion here because PHPStan complains that $data->foo
        // is an undefined property. PHPStan needs to be updated to respect
        // `assertObjectHasProperty()` like it respects `assertArrayHasKey()`.
        assert(isset($data->foo));
        $this->assertSame('bar', $data->foo);
    }

    public function testValidateWithObject(): void
    {
        $object = (object) ['baz' => 'qux'];
        $data = $this->validate($object, fn (object $data): bool => isset($data->baz));

        $this->assertObjectHasProperty('baz', $data);

        // Using an assertion here because PHPStan complains that $data->baz
        // is an undefined property. PHPStan needs to be updated to respect
        // `assertObjectHasProperty()` like it respects `assertArrayHasKey()`.
        assert(isset($data->baz));
        $this->assertSame('qux', $data->baz);
    }
}
