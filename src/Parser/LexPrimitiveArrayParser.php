<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveArray;

use function json_encode;
use function sprintf;

use const JSON_UNESCAPED_SLASHES;

/**
 * @phpstan-import-type TLexPrimitiveArray from LexPrimitiveArray
 */
class LexPrimitiveArrayParser extends LexArrayParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexArray
    {
        /** @var TLexPrimitiveArray $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexPrimitiveArray(
            description: $data->description ?? null,
            items: $this->parseItems($data),
            minLength: $data->minLength ?? null,
            maxLength: $data->maxLength ?? null,
        );
    }

    /**
     * @phpstan-param TLexPrimitiveArray $data
     */
    private function parseItems(object $data): LexPrimitive | null
    {
        $parsedItems = null;
        if (isset($data->items)) {
            $parsedItems = $this->getParserFactory()->getParser(LexiconParser::class)->parse($data->items);
        }

        if (
            $parsedItems === null
            || $parsedItems instanceof LexPrimitive
        ) {
            return $parsedItems;
        }

        throw new UnableToParse(sprintf(
            'The input data does not contain a valid schema definition: "%s"',
            json_encode($data, JSON_UNESCAPED_SLASHES),
        ));
    }
}
