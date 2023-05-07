<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon;

use SocialWeb\Atproto\Lexicon\LexiconException;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexiconParser;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;
use Symfony\Component\Finder\Finder;

use function array_reduce;
use function assert;
use function is_string;
use function json_encode;
use function realpath;

use const PHP_EOL;

/**
 * This test uses the Git submodule included in resources/bluesky-social
 * to test parsing the actual Lexicon schemas created by Bluesky.
 * If the submodule hasn't been initialized, we will skip this test.
 */
class ParseCanonicalSchemasTest extends TestCase
{
    private const SCHEMA_DIR = __DIR__ . '/../resources/bluesky-social/lexicons';

    private string | bool $schemaDir = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schemaDir = realpath(self::SCHEMA_DIR);

        if ($this->schemaDir === false) {
            $this->markTestSkipped('Git submodules have not been initialized');
        }
    }

    public function testParsingCanonicalSchemas(): void
    {
        assert(is_string($this->schemaDir));

        $finder = new Finder();
        $finder->in($this->schemaDir)->files()->name('*.json');

        $schemaRepository = new DefaultSchemaRepository($this->schemaDir);
        $parser = new LexiconParser(new DefaultParserFactory($schemaRepository));

        /** @var array<array{file: string, error: string}> $failedSchemas */
        $failedSchemas = [];

        foreach ($finder as $file) {
            try {
                /** @var LexiconDoc $document */
                $document = $parser->parse($file->getContents());
                $schemaFile = $schemaRepository->findSchemaPathByNsid($document->id);

                // Make sure our NSID parsing is correct.
                $this->assertSame($file->getRealPath(), $schemaFile);

                // Make sure the structure we created matches the original document.
                $this->assertJsonStringEqualsJsonString($file->getContents(), (string) json_encode($document));
            } catch (LexiconException $exception) {
                $failedSchemas[] = [
                    'file' => $file->getRealPath(),
                    'error' => $exception->getMessage(),
                ];
            }
        }

        if ($failedSchemas) {
            $this->fail(
                'Unable to parse the following schemas: ' . PHP_EOL . PHP_EOL
                . array_reduce(
                    $failedSchemas,
                    /** @param array{file: string, error: string} $v */
                    fn (string $c, array $v): string => $c
                        . $v['file'] . ':' . PHP_EOL . $v['error'] . PHP_EOL . PHP_EOL,
                    '',
                ),
            );
        }

        $this->assertSame([], $failedSchemas);
    }
}
