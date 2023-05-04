<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;

use function array_reduce;
use function is_object;
use function is_string;
use function json_encode;
use function sprintf;

use const JSON_UNESCAPED_SLASHES;

final class LexObjectParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexObject
    {
        /** @var object{properties?: object, required?: string[], description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'object'
                && (!isset($data->properties) || is_object($data->properties))
                && (!isset($data->required) || $this->isArrayOfString($data->required))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexObject(
            properties: $this->parseProperties($data),
            required: $data->required ?? null,
            description: $data->description ?? null,
        );
    }

    /**
     * @param object{properties?: object, required?: string[], description?: string} $data
     *
     * @return array<string, LexArray | LexBlob | LexObject | LexPrimitive | LexRef | LexRefUnion | LexUnknown>
     */
    private function parseProperties(object $data): array
    {
        $properties = $data->properties ?? (object) [];

        $parsedProperties = [];

        /**
         * @var string $name
         * @var object $property
         */
        foreach ($properties as $name => $property) {
            $parsedProperties[$name] = $this->getParserFactory()->getParser(LexiconParser::class)->parse($property);
        }

        $isValid = array_reduce(
            $parsedProperties,
            fn (bool $carry, mixed $value): bool => $carry
                && (
                    $value instanceof LexArray
                    || $value instanceof LexBlob
                    || $value instanceof LexObject
                    || $value instanceof LexPrimitive
                    || $value instanceof LexRef
                    || $value instanceof LexRefUnion
                    || $value instanceof LexUnknown
                ),
            true,
        );

        if ($parsedProperties === [] || $isValid) {
            /** @var array<string, LexArray | LexBlob | LexObject | LexPrimitive | LexRef | LexRefUnion | LexUnknown> */
            return $parsedProperties;
        }

        throw new UnableToParse(sprintf(
            'The input data does not contain a valid schema definition: "%s"',
            json_encode($data, JSON_UNESCAPED_SLASHES),
        ));
    }
}
