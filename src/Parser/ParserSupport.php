<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;

use function is_object;
use function is_string;
use function json_decode;
use function json_encode;
use function sprintf;

use const JSON_UNESCAPED_SLASHES;

trait ParserSupport
{
    private ?ParserFactory $parserFactory = null;
    private ?SchemaRepository $schemaRepository = null;

    public function getParserFactory(): ParserFactory
    {
        if ($this->parserFactory === null) {
            throw new InvalidParserConfiguration('Please configure this parser with a parser factory');
        }

        return $this->parserFactory;
    }

    public function getSchemaRepository(): SchemaRepository
    {
        if ($this->schemaRepository === null) {
            throw new InvalidParserConfiguration('Please configure this parser with a schema repository');
        }

        return $this->schemaRepository;
    }

    public function setParserFactory(ParserFactory $parserFactory): void
    {
        $this->parserFactory = $parserFactory;
    }

    public function setSchemaRepository(SchemaRepository $schemaRepository): void
    {
        $this->schemaRepository = $schemaRepository;
    }

    /**
     * @param Closure(object): bool $validator
     *
     * @throws UnableToParse
     */
    private function validate(object | string $data, Closure $validator): object
    {
        if (is_string($data)) {
            /** @var object | null $decoded */
            $decoded = json_decode($data);

            if (is_object($decoded)) {
                $data = $decoded;
            }
        }

        if (!is_object($data) || !$validator($data)) {
            throw new UnableToParse(sprintf(
                'The input data does not contain a valid schema definition: "%s"',
                is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_SLASHES),
            ));
        }

        return $data;
    }
}
