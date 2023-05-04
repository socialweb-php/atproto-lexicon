<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcMethodType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;

use function array_reduce;
use function is_array;
use function is_object;
use function is_string;
use function json_encode;
use function sprintf;

use const JSON_UNESCAPED_SLASHES;

/**
 * @internal
 */
abstract class LexXrpcMethodParser implements Parser
{
    use ParserSupport;

    protected function parseMethod(
        object | string $data,
        LexXrpcMethodType $method,
    ): LexXrpcQuery | LexXrpcProcedure {
        /** @var object{type: 'query' | 'procedure', parameters?: array<string, object>, errors?: object[], input?: object, output?: object, description?: string} $data */
        $data = $this->validate($data, $this->getValidator($method));

        $parameters = $this->parseParameters($data);
        $errors = $this->parseErrors($data);
        $output = $this->parseBody($data->output ?? null);

        if ($method === LexXrpcMethodType::Procedure) {
            $input = $this->parseBody($data->input ?? null);

            return new LexXrpcProcedure(
                parameters: $parameters,
                input: $input,
                output: $output,
                errors: $errors,
                description: $data->description ?? null,
            );
        }

        return new LexXrpcQuery(
            parameters: $parameters,
            output: $output,
            errors: $errors,
            description: $data->description ?? null,
        );
    }

    /**
     * @return array<string, LexPrimitive> | null
     */
    private function parseParameters(object $data): ?array
    {
        /** @var array<string, object> $parameters */
        $parameters = $data->parameters ?? [];
        $parsedParameters = [];

        foreach ($parameters as $name => $value) {
            $parsedParameters[$name] = $this->getParserFactory()->getParser(LexiconParser::class)->parse($value);
        }

        if ($parsedParameters === []) {
            return null;
        }

        $isValid = array_reduce($parsedParameters, $this->getParameterValidator(), true);

        if ($isValid) {
            /** @var array<string, LexPrimitive> */
            return $parsedParameters;
        }

        throw new UnableToParse(sprintf(
            'The input data does not contain a valid schema definition: "%s"',
            json_encode($data, JSON_UNESCAPED_SLASHES),
        ));
    }

    /**
     * @return LexXrpcError[] | null
     */
    private function parseErrors(object $data): ?array
    {
        /** @var object[] $errors */
        $errors = $data->errors ?? [];
        $parsedErrors = [];

        foreach ($errors as $value) {
            $parsedErrors[] = $this->getParserFactory()->getParser(LexXrpcErrorParser::class)->parse($value);
        }

        return $parsedErrors ?: null;
    }

    private function parseBody(?object $body): ?LexXrpcBody
    {
        if ($body === null) {
            return null;
        }

        return $this->getParserFactory()->getParser(LexXrpcBodyParser::class)->parse($body);
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(LexXrpcMethodType $method): Closure
    {
        return function (object $data) use ($method): bool {
            $isInputValid = true;
            if ($method === LexXrpcMethodType::Procedure) {
                $isInputValid = (!isset($data->input) || is_object($data->input));
            }

            return isset($data->type) && $data->type === $method->value
                && (!isset($data->parameters) || is_object($data->parameters))
                && (!isset($data->errors) || is_array($data->errors))
                && (!isset($data->output) || is_object($data->output))
                && (!isset($data->description) || is_string($data->description))
                && $isInputValid;
        };
    }

    /**
     * @return Closure(bool, mixed): bool
     */
    private function getParameterValidator(): Closure
    {
        return fn (bool $carry, mixed $value): bool => $carry
            && $value instanceof LexPrimitive;
    }
}
