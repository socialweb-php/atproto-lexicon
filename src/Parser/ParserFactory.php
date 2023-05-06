<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexType;

interface ParserFactory
{
    /**
     * Returns a parser by class name.
     *
     * @param class-string<T> $className
     *
     * @return T
     *
     * @template T of Parser
     */
    public function getParser(string $className): Parser;

    /**
     * Returns a parser by type name (i.e., "array," "bytes," "string," etc.).
     *
     * @see LexType for a list of type names
     */
    public function getParserByTypeName(string $typeName): Parser;

    public function getSchemaRepository(): SchemaRepository;
}
