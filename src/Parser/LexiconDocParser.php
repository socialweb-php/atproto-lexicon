<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Types\LexEntity;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexiconDoc;

use function is_float;
use function is_int;
use function is_object;
use function is_string;

/**
 * @phpstan-import-type TLexiconDoc from LexiconDoc
 */
class LexiconDocParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexiconDoc
    {
        /** @var TLexiconDoc $data */
        $data = $this->validate($data, $this->getValidator());

        $nsid = new Nsid($data->id);

        $existingDoc = $this->getParserFactory()->getSchemaRepository()->findSchemaByNsid($nsid);
        if ($existingDoc !== null) {
            return $existingDoc;
        }

        $doc = new LexiconDoc(
            id: $nsid,
            revision: $data->revision ?? null,
            description: $data->description ?? null,
            defs: $this->parseDefs($data),
        );

        $this->getParserFactory()->getSchemaRepository()->storeSchema($doc);

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

        foreach ($defs as $defId => $def) {
            $parsedDef = $this->getParserFactory()->getParser(LexiconParser::class)->parse($def);

            /**
             * @psalm-suppress NoInterfaceProperties
             * @var LexType | null $defType
             */
            $defType = $parsedDef->type ?? null;

            if (
                $defId !== LexiconDoc::MAIN
                && (
                    $defType === LexType::Record
                    || $defType === LexType::Procedure
                    || $defType === LexType::Query
                    || $defType === LexType::Subscription
                )
            ) {
                throw new UnableToParse(
                    'Records, procedures, queries, and subscriptions must be in the main definition',
                );
            }

            $parsedDefs[$defId] = $parsedDef;
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
