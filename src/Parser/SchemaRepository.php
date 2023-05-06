<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

use function explode;
use function implode;
use function is_string;
use function realpath;

use const DIRECTORY_SEPARATOR;

class SchemaRepository
{
    use IsArrayOf;

    /**
     * @var list<string>
     */
    public readonly array $schemaDirectories;

    /**
     * @param string | list<string> $schemaDirectory The directory (or
     *     directories) where all the schemas are located. The folder structure
     *     in the directories should be organized according to the schema NSIDs.
     * @param array<string, LexiconDoc> $parsedSchemas An array for storing and
     *     retrieving already-parsed schemas.
     */
    public function __construct(array | string $schemaDirectory, private array $parsedSchemas = [])
    {
        if (is_string($schemaDirectory)) {
            $schemaDirectory = [$schemaDirectory];
        }

        if (!$this->isArrayOfString($schemaDirectory)) {
            throw new SchemaNotFound('All schema directory paths must be strings');
        }

        $directories = [];
        foreach ($schemaDirectory as $directory) {
            $realSchemaDirectory = realpath($directory);

            if ($realSchemaDirectory === false) {
                throw new SchemaNotFound("Unable to find schema directory at \"$directory\"");
            }

            $directories[] = $realSchemaDirectory;
        }

        $this->schemaDirectories = $directories;
    }

    public function findSchemaByNsid(Nsid $nsid): ?LexiconDoc
    {
        return $this->parsedSchemas[$nsid->nsid] ?? null;
    }

    public function storeSchema(LexiconDoc $lexiconDoc): void
    {
        $this->parsedSchemas[$lexiconDoc->id->nsid] = $lexiconDoc;
    }

    /**
     * Returns the absolute path to the schema file, if the file exists.
     *
     * When more than one schema directory is configured, there is a possibility
     * of conflicts; this method returns the first file found.
     */
    public function findSchemaPathByNsid(Nsid $nsid): ?string
    {
        $pathParts = explode('.', $nsid->nsid);
        $fileName = implode(DIRECTORY_SEPARATOR, $pathParts) . '.json';

        foreach ($this->schemaDirectories as $schemaDirectory) {
            $filePath = realpath($schemaDirectory . DIRECTORY_SEPARATOR . $fileName);

            if ($filePath !== false) {
                return $filePath;
            }
        }

        return null;
    }
}
