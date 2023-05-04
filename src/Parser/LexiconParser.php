<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcProcedure;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcQuery;

use function assert;
use function is_object;
use function is_string;
use function json_encode;

final class LexiconParser implements Parser
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

    public function parse(object | string $data): LexType
    {
        $data = $this->validate($data, fn (): bool => true);

        if (isset($data->lexicon) && isset($data->id)) {
            return $this->getParserFactory()->getParser(LexiconDocParser::class)->parse($data);
        }

        /** @var string | null $type */
        $type = $data->type ?? null;

        if (is_string($type)) {
            return match ($type) {
                'procedure' => $this->parseProcedure($data),
                'query' => $this->parseQuery($data),
                default => $this->getParserFactory()->getParserByTypeName($type)->parse($data),
            };
        }

        throw new UnableToParse('Unknown object: ' . json_encode($data));
    }

    /**
     * @param 'query' | 'procedure' $queryOrProcedure
     */
    private function parseMethod(object $def, string $queryOrProcedure): LexXrpcQuery | LexXrpcProcedure
    {
        $parameters = [];
        $errors = [];

        foreach ($def->parameters ?? [] as $name => $param) {
            assert(is_string($name));
            assert(is_object($param));
            $param = $this->parse($param);
            assert($param instanceof LexPrimitive);
            $parameters[$name] = $param;
        }

        foreach ($def->errors ?? [] as $error) {
            assert(is_object($error));
            $errors[] = (new LexXrpcErrorParser())->parse($error);
        }

        $input = $def->input ?? null;
        $output = $def->output ?? null;
        $description = $def->description ?? null;

        assert($input === null || is_object($input));
        assert($output === null || is_object($output));
        assert($description === null || is_string($description));

        if ($input !== null) {
            $input = $this->getParserFactory()->getParser(LexXrpcBodyParser::class)->parse($input);
        }

        if ($output !== null) {
            $output = $this->getParserFactory()->getParser(LexXrpcBodyParser::class)->parse($output);
        }

        if ($queryOrProcedure === 'procedure') {
            return new LexXrpcProcedure($parameters ?: null, $input, $output, $errors ?: null, $description);
        }

        return new LexXrpcQuery($parameters ?: null, $output, $errors ?: null, $description);
    }

    private function parseProcedure(object $def): LexXrpcProcedure
    {
        /** @var LexXrpcProcedure */
        return $this->parseMethod($def, 'procedure');
    }

    private function parseQuery(object $def): LexXrpcQuery
    {
        /** @var LexXrpcQuery */
        return $this->parseMethod($def, 'query');
    }
}
