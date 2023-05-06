<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveArray;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_int;
use function is_object;
use function is_string;
use function json_encode;
use function sprintf;

use const JSON_UNESCAPED_SLASHES;

/**
 * @phpstan-import-type LexPrimitiveArrayJson from LexPrimitiveArray
 */
final class LexPrimitiveArrayParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexArray
    {
        /** @var LexPrimitiveArrayJson $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexPrimitiveArray(
            description: $data->description ?? null,
            items: $this->parseItems($data),
            minLength: $data->minLength ?? null,
            maxLength: $data->maxLength ?? null,
        );
    }

    /**
     * @phpstan-param LexPrimitiveArrayJson $data
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

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Array->value
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->items) || is_object($data->items))
            && (!isset($data->minLength) || is_int($data->minLength))
            && (!isset($data->maxLength) || is_int($data->maxLength));
    }
}
