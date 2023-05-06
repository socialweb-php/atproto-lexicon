<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcParameters;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcSubscription;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcSubscriptionMessage;
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
 * @phpstan-import-type TLexXrpcSubscription from LexXrpcSubscription
 * @phpstan-import-type TLexXrpcSubscriptionMessage from LexXrpcSubscriptionMessage
 * @phpstan-type TLexXrpc = TLexXrpcProcedure | TLexXrpcQuery | TLexXrpcSubscription
 */
abstract class LexXrpcParser implements Parser
{
    use ParserSupport;

    protected function parseXrpc(
        object | string $data,
        LexXrpcType $xrpcType,
    ): LexXrpcQuery | LexXrpcProcedure | LexXrpcSubscription {
        /** @var TLexXrpc $data */
        $data = $this->validate($data, $this->getValidator($xrpcType));

        return match ($xrpcType) {
            LexXrpcType::Procedure => $this->parseProcedure($data),
            LexXrpcType::Query => $this->parseQuery($data),
            LexXrpcType::Subscription => $this->parseSubscription($data),
        };
    }

    /**
     * @phpstan-param TLexXrpc $data
     */
    private function parseProcedure(object $data): LexXrpcProcedure
    {
        /** @phpstan-var TLexXrpcProcedure $data */
        return new LexXrpcProcedure(
            description: $data->description ?? null,
            parameters: $this->parseParameters($data->parameters ?? null),
            input: $this->parseBody($data->input ?? null),
            output: $this->parseBody($data->output ?? null),
            errors: $this->parseErrors($data->errors ?? []),
        );
    }

    /**
     * @phpstan-param TLexXrpc $data
     */
    private function parseQuery(object $data): LexXrpcQuery
    {
        /** @phpstan-var TLexXrpcQuery $data */
        return new LexXrpcQuery(
            description: $data->description ?? null,
            parameters: $this->parseParameters($data->parameters ?? null),
            output: $this->parseBody($data->output ?? null),
            errors: $this->parseErrors($data->errors ?? []),
        );
    }

    /**
     * @phpstan-param TLexXrpc $data
     */
    private function parseSubscription(object $data): LexXrpcSubscription
    {
        /** @phpstan-var TLexXrpcSubscription $data */
        return new LexXrpcSubscription(
            description: $data->description ?? null,
            parameters: $this->parseParameters($data->parameters ?? null),
            message: $this->parseMessage($data->message ?? null),
            infos: $this->parseErrors($data->infos ?? []),
            errors: $this->parseErrors($data->errors ?? []),
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
     * @param list<TLexXrpcError> $errors
     *
     * @return list<LexXrpcError> | null
     */
    private function parseErrors(array $errors): ?array
    {
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
     * @phpstan-param TLexXrpcSubscriptionMessage | null $message
     */
    private function parseMessage(?object $message): ?LexXrpcSubscriptionMessage
    {
        if ($message === null) {
            return null;
        }

        return $this->getParserFactory()->getParser(LexXrpcSubscriptionMessageParser::class)->parse($message);
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(LexXrpcType $method): Closure
    {
        return function (object $data) use ($method): bool {
            $isProcedureValid = true;
            $isSubscriptionValid = true;

            if ($method === LexXrpcType::Procedure) {
                $isProcedureValid = (!isset($data->input) || is_object($data->input));
            }

            if ($method === LexXrpcType::Subscription) {
                $isSubscriptionValid = (!isset($data->message) || is_object($data->message))
                    && (!isset($data->infos) || is_array($data->infos));
            }

            return isset($data->type) && $data->type === $method->value
                && (!isset($data->description) || is_string($data->description))
                && (!isset($data->parameters) || is_object($data->parameters))
                && (!isset($data->output) || is_object($data->output))
                && (!isset($data->errors) || is_array($data->errors))
                && $isProcedureValid
                && $isSubscriptionValid;
        };
    }
}
