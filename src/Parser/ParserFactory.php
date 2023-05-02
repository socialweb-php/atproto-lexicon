<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use function class_exists;
use function is_a;

class ParserFactory
{
    /**
     * @var array<string, class-string<Parser> | null>
     */
    private array $typeParserMap = [
        'array' => null,
        'audio' => LexAudioParser::class,
        'blob' => LexBlobParser::class,
        'boolean' => LexBooleanParser::class,
        'image' => LexImageParser::class,
        'integer' => LexIntegerParser::class,
        'number' => null,
        'object' => null,
        'procedure' => null,
        'query' => null,
        'record' => null,
        'ref' => null,
        'string' => null,
        'token' => null,
        'union' => null,
        'unknown' => null,
        'video' => null,
    ];

    /**
     * @param array<class-string<Parser>, Parser> $parsers
     */
    public function __construct(
        private readonly SchemaRepository $schemaRepository,
        private array $parsers = [],
    ) {
    }

    /**
     * @param class-string<T> $name
     *
     * @return T
     *
     * @template T of Parser
     */
    public function getParser(string $name): Parser
    {
        if (isset($this->parsers[$name])) {
            /** @var T */
            return $this->parsers[$name];
        }

        if (!class_exists($name) || !is_a($name, Parser::class, true)) {
            throw new ParserNotFound("Unable to find parser \"$name\"");
        }

        $parser = new $name();
        $parser->setParserFactory($this);
        $parser->setSchemaRepository($this->schemaRepository);

        $this->parsers[$name] = $parser;

        /** @var T */
        return $parser;
    }

    public function getParserByTypeName(string $typeName): Parser
    {
        $className = $this->typeParserMap[$typeName] ?? null;

        if ($className === null) {
            throw new ParserNotFound("Unable to find parser for \"$typeName\"");
        }

        return $this->getParser($className);
    }
}
