<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

use function is_float;
use function is_int;
use function is_object;
use function is_string;

/**
 * @phpstan-import-type LexiconDocJson from LexiconDoc
 */
class LexiconDocParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexiconDoc
    {
        /** @var LexiconDocJson $data */
        $data = $this->validate($data, $this->getValidator());

        $nsid = new Nsid($data->id);

        $existingDoc = $this->getSchemaRepository()->findSchemaByNsid($nsid);
        if ($existingDoc !== null) {
            return $existingDoc;
        }

        $doc = new LexiconDoc(
            id: $nsid,
            revision: $data->revision ?? null,
            description: $data->description ?? null,
            defs: $this->parseDefs($data),
        );

        $this->getSchemaRepository()->storeSchema($doc);

        return $doc;
    }

    /**
     * @return array<string, LexEntity>
     */
    private function parseDefs(object $data): array
    {
        /** @var array<string, object> $defs */
        $defs = $data->defs ?? (object) [];
        $parsedDefs = [];

        foreach ($defs as $name => $def) {
            $parsedDefs[$name] = $this->getParserFactory()->getParser(LexiconParser::class)->parse($def);
        }

        return $parsedDefs;
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->lexicon) && $data->lexicon === 1
            && Nsid::isValid($data->id ?? null)
            && (!isset($data->revision) || is_int($data->revision) || is_float($data->revision))
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->defs) || is_object($data->defs));
    }
}
