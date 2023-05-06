<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveArray;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcParameters;

use function array_reduce;
use function is_object;
use function is_string;

/**
 * @phpstan-import-type TLexPrimitive from LexPrimitive
 * @phpstan-import-type TLexPrimitiveArray from LexPrimitiveArray
 * @phpstan-import-type TLexXrpcParameters from LexXrpcParameters
 */
class LexXrpcParametersParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexXrpcParameters
    {
        /** @var TLexXrpcParameters $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexXrpcParameters(
            description: $data->description ?? null,
            required: $data->required ?? null,
            properties: $this->parseProperties($data),
        );
    }

    /**
     * @return array<string, LexPrimitive | LexPrimitiveArray>
     */
    private function parseProperties(object $data): array
    {
        $parsedProperties = [];

        /** @var array<string, TLexPrimitive | TLexPrimitiveArray> $properties */
        $properties = $data->properties ?? [];

        foreach ($properties as $property => $value) {
            $parsedProperties[$property] = $this->getParserFactory()->getParser(LexiconParser::class)->parse($value);
        }

        $isValid = array_reduce($parsedProperties, $this->getPropertyValidator(), true);

        if ($parsedProperties === [] || $isValid) {
            /** @var array<string, LexPrimitive | LexPrimitiveArray> */
            return $parsedProperties;
        }

        $this->throwParserError($data);
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Params->value
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->required) || $this->isArrayOfString($data->required))
            && (!isset($data->properties) || is_object($data->properties));
    }

    /**
     * @return Closure(bool, mixed): bool
     */
    private function getPropertyValidator(): Closure
    {
        return fn (bool $carry, mixed $value): bool => $carry
            && (
                $value instanceof LexPrimitive
                || $value instanceof LexPrimitiveArray
            );
    }
}
