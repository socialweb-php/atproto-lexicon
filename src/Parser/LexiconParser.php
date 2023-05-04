<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_string;
use function json_encode;

final class LexiconParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function __construct(
        ?SchemaRepository $schemaRepository = null,
        ?ParserFactory $parserFactory = null,
    ) {
        if ($schemaRepository !== null) {
            $this->setSchemaRepository($schemaRepository);
        }

        if ($parserFactory !== null) {
            $this->setParserFactory($parserFactory);
        }
    }

    public function parse(object | string $data): LexType
    {
        $data = $this->validate($data, fn (): bool => true);

        if (isset($data->lexicon) && isset($data->id)) {
            return $this->getParserFactory()->getParser(LexiconDocParser::class)->parse($data);
        }

        /** @var string | null $type */
        $type = $data->type ?? null;

        if (is_string($type)) {
            return $this->getParserFactory()->getParserByTypeName($type)->parse($data);
        }

        throw new UnableToParse('Unknown object: ' . json_encode($data));
    }
}
