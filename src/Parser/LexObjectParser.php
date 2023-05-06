<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexArray;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function array_reduce;
use function is_object;
use function is_string;
use function json_encode;
use function sprintf;

use const JSON_UNESCAPED_SLASHES;

/**
 * @phpstan-import-type TLexObject from LexObject
 */
class LexObjectParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexObject
    {
        /** @var TLexObject $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexObject(
            description: $data->description ?? null,
            required: $data->required ?? null,
            nullable: $data->nullable ?? null,
            properties: $this->parseProperties($data),
        );
    }

    /**
     * @return array<string, LexArray | LexBlob | LexPrimitive | LexRef | LexRefUnion>
     *
     * @phpstan-param TLexObject $data
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

        $isValid = array_reduce($parsedProperties, $this->getPropertyValidator(), true);

        if ($parsedProperties === [] || $isValid) {
            /** @var array<string, LexArray | LexBlob | LexPrimitive | LexRef | LexRefUnion> */
            return $parsedProperties;
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
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Object->value
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->required) || $this->isArrayOfString($data->required))
            && (!isset($data->nullable) || $this->isArrayOfString($data->nullable))
            && (!isset($data->properties) || is_object($data->properties));
    }

    /**
     * @return Closure(bool, mixed): bool
     */
    private function getPropertyValidator(): Closure
    {
        return fn (bool $carry, mixed $value): bool => $carry
            && (
                $value instanceof LexArray
                || $value instanceof LexBlob
                || $value instanceof LexPrimitive
                || $value instanceof LexRef
                || $value instanceof LexRefUnion
            );
    }
}
