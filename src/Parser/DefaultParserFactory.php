<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use function class_exists;
use function is_a;

class DefaultParserFactory implements ParserFactory
{
    /**
     * @var array<string, class-string<Parser> | null>
     */
    private array $typeParserMap = [
        'array' => LexArrayParser::class,
        'blob' => LexBlobParser::class,
        'boolean' => LexBooleanParser::class,
        'bytes' => LexBytesParser::class,
        'cid-link' => LexCidLinkParser::class,
        'integer' => LexIntegerParser::class,
        'object' => LexObjectParser::class,
        'params' => LexXrpcParametersParser::class,
        'procedure' => LexXrpcProcedureParser::class,
        'query' => LexXrpcQueryParser::class,
        'record' => LexRecordParser::class,
        'ref' => LexRefParser::class,
        'string' => LexStringParser::class,
        'subscription' => LexXrpcSubscriptionParser::class,
        'token' => LexTokenParser::class,
        'union' => LexRefUnionParser::class,
        'unknown' => LexUnknownParser::class,
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
     * @param class-string<T> $className
     *
     * @return T
     *
     * @template T of Parser
     */
    public function getParser(string $className): Parser
    {
        if (isset($this->parsers[$className])) {
            /** @var T */
            return $this->parsers[$className];
        }

        if (!class_exists($className) || !is_a($className, Parser::class, true)) {
            throw new ParserNotFound("Unable to find parser \"$className\"");
        }

        $parser = new $className();
        $parser->setParserFactory($this);

        $this->parsers[$className] = $parser;

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

    public function getSchemaRepository(): SchemaRepository
    {
        return $this->schemaRepository;
    }
}
