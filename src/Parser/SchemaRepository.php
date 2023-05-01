<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

use function explode;
use function implode;
use function realpath;

use const DIRECTORY_SEPARATOR;

final class SchemaRepository
{
    public readonly string $schemaDirectory;

    /**
     * @param string $schemaDirectory The directory where all the schemas are
     *     located. The folder structure in this directory should be organized
     *     according to the schema NSIDs.
     * @param array<string, LexiconDoc> $parsedSchemas An array for storing and
     *     retrieving already-parsed schemas.
     */
    public function __construct(string $schemaDirectory, private array $parsedSchemas = [])
    {
        $realSchemaDirectory = realpath($schemaDirectory);

        if ($realSchemaDirectory === false) {
            throw new SchemaNotFound("Unable to find schema directory at \"$schemaDirectory\"");
        }

        $this->schemaDirectory = $realSchemaDirectory;
    }

    public function findSchemaByNsid(Nsid $nsid): ?LexiconDoc
    {
        return $this->parsedSchemas[$nsid->nsid] ?? null;
    }

    public function storeSchema(LexiconDoc $lexiconDoc): void
    {
        $this->parsedSchemas[$lexiconDoc->id] = $lexiconDoc;
    }

    /**
     * Returns the absolute path to the schema file, if the file exists.
     */
    public function findSchemaPathByNsid(Nsid $nsid): ?string
    {
        $pathParts = explode('.', $nsid->nsid);
        $fileName = implode(DIRECTORY_SEPARATOR, $pathParts) . '.json';
        $filePath = $this->schemaDirectory . DIRECTORY_SEPARATOR . $fileName;

        return realpath($filePath) ?: null;
    }
}
