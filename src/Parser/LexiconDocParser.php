<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

use function is_float;
use function is_int;
use function is_object;
use function is_string;

final class LexiconDocParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexiconDoc
    {
        /** @var object{id: string, revision?: float | int, description?: string, defs?: object} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->lexicon)
                && $data->lexicon === 1
                && Nsid::isValid($data->id ?? null)
                && (!isset($data->revision) || is_int($data->revision) || is_float($data->revision))
                && (!isset($data->description) || is_string($data->description))
                && (!isset($data->defs) || is_object($data->defs)),
        );

        $nsid = new Nsid($data->id);

        $existingDoc = $this->getSchemaRepository()->findSchemaByNsid($nsid);
        if ($existingDoc !== null) {
            return $existingDoc;
        }

        $revision = $data->revision ?? null;
        $description = $data->description ?? null;
        $defs = $data->defs ?? (object) [];

        $parsedDefs = [];

        /**
         * @var string $name
         * @var object $def
         */
        foreach ($defs as $name => $def) {
            $parsedDefs[$name] = $this->getParserFactory()->getParser(LexiconParser::class)->parse($def);
        }

        $doc = new LexiconDoc(
            id: $nsid,
            defs: $parsedDefs,
            revision: $revision,
            description: $description,
        );

        $this->getSchemaRepository()->storeSchema($doc);

        return $doc;
    }
}
