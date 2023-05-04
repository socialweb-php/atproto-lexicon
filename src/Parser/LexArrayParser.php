<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexUnion;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;

use function is_int;
use function is_object;
use function is_string;
use function json_encode;
use function sprintf;

use const JSON_UNESCAPED_SLASHES;

final class LexArrayParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexArray
    {
        /** @var object{items?: object, minLength?: int, maxLength?: int, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'array'
                && (!isset($data->items) || is_object($data->items))
                && (!isset($data->minLength) || is_int($data->minLength))
                && (!isset($data->maxLength) || is_int($data->maxLength))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexArray(
            items: $this->parseItems($data),
            minLength: $data->minLength ?? null,
            maxLength: $data->maxLength ?? null,
            description: $data->description ?? null,
        );
    }

    /**
     * @param object{items?: object, minLength?: int, maxLength?: int, description?: string} $data
     */
    private function parseItems(object $data): LexObject | LexPrimitive | LexRef | LexUnion | LexUnknown | null
    {
        $parsedItems = null;
        if (isset($data->items)) {
            $parsedItems = $this->getParserFactory()->getParser(LexiconParser::class)->parse($data->items);
        }

        if (
            $parsedItems === null
            || $parsedItems instanceof LexObject
            || $parsedItems instanceof LexPrimitive
            || $parsedItems instanceof LexRef
            || $parsedItems instanceof LexUnion
            || $parsedItems instanceof LexUnknown
        ) {
            return $parsedItems;
        }

        throw new UnableToParse(sprintf(
            'The input data does not contain a valid schema definition: "%s"',
            json_encode($data, JSON_UNESCAPED_SLASHES),
        ));
    }
}
