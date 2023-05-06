<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexEntity;

use function is_string;
use function json_encode;
use function sprintf;

class LexiconParser implements Parser
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

    public function parse(object | string $data): LexEntity
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

        throw new UnableToParse(sprintf(
            'The input data does not contain a valid schema definition: "%s"',
            json_encode($data),
        ));
    }
}
