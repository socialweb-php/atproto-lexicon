<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

interface SchemaRepository
{
    public function findSchemaByNsid(Nsid $nsid): ?LexiconDoc;

    /**
     * Returns the absolute path to the schema file, if the file exists.
     *
     * When more than one schema directory is configured, there is a possibility
     * of conflicts; this method returns the first file found.
     */
    public function findSchemaPathByNsid(Nsid $nsid): ?string;

    public function storeSchema(LexiconDoc $lexiconDoc): void;
}
