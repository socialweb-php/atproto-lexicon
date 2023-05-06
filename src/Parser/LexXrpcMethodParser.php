<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcParameters;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcType;

use function is_array;
use function is_object;
use function is_string;

/**
 * @internal
 *
 * @phpstan-import-type TLexXrpcBody from LexXrpcBody
 * @phpstan-import-type TLexXrpcError from LexXrpcError
 * @phpstan-import-type TLexXrpcParameters from LexXrpcParameters
 * @phpstan-import-type TLexXrpcProcedure from LexXrpcProcedure
 * @phpstan-import-type TLexXrpcQuery from LexXrpcQuery
 */
abstract class LexXrpcMethodParser implements Parser
{
    use ParserSupport;

    protected function parseMethod(
        object | string $data,
        LexXrpcType $method,
    ): LexXrpcQuery | LexXrpcProcedure {
        /** @var TLexXrpcProcedure | TLexXrpcQuery $data */
        $data = $this->validate($data, $this->getValidator($method));

        $parameters = $this->parseParameters($data->parameters ?? null);
        $errors = $this->parseErrors($data);
        $output = $this->parseBody($data->output ?? null);

        if ($method === LexXrpcType::Procedure) {
            /** @var TLexXrpcBody | null $input */
            $input = $data->input ?? null;

            return new LexXrpcProcedure(
                description: $data->description ?? null,
                parameters: $parameters,
                input: $this->parseBody($input),
                output: $output,
                errors: $errors,
            );
        }

        return new LexXrpcQuery(
            description: $data->description ?? null,
            parameters: $parameters,
            output: $output,
            errors: $errors,
        );
    }

    /**
     * @phpstan-param TLexXrpcParameters | null $parameters
     */
    private function parseParameters(?object $parameters): ?LexXrpcParameters
    {
        if ($parameters === null) {
            return null;
        }

        return $this->getParserFactory()->getParser(LexXrpcParametersParser::class)->parse($parameters);
    }

    /**
     * @return list<LexXrpcError> | null
     */
    private function parseErrors(object $data): ?array
    {
        /** @var TLexXrpcError[] $errors */
        $errors = $data->errors ?? [];
        $parsedErrors = [];

        foreach ($errors as $value) {
            $parsedErrors[] = $this->getParserFactory()->getParser(LexXrpcErrorParser::class)->parse($value);
        }

        return $parsedErrors ?: null;
    }

    /**
     * @phpstan-param TLexXrpcBody | null $body
     */
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
    private function getValidator(LexXrpcType $method): Closure
    {
        return function (object $data) use ($method): bool {
            $isInputValid = true;
            if ($method === LexXrpcType::Procedure) {
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
}
