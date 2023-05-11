<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;
use SocialWeb\Atproto\Lexicon\Nsid\InvalidNsid;
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Parser\LexiconParser;
use SocialWeb\Atproto\Lexicon\Parser\ParserFactory;

use function assert;
use function file_get_contents;
use function is_array;
use function json_encode;
use function sprintf;
use function str_starts_with;

use const JSON_UNESCAPED_SLASHES;

/**
 * @phpstan-type TLexRef = object{
 *     type: 'ref',
 *     description?: string,
 *     ref: string,
 * }
 */
class LexRef implements JsonSerializable, LexEntity, LexResolvable
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?string $ref = null,
        private readonly ?ParserFactory $parserFactory = null,
    ) {
        $this->type = LexType::Ref;
    }

    public function resolve(): LexEntity
    {
        if ($this->parserFactory === null) {
            throw new ParserFactoryRequired(
                'You must provide a ParserFactory to the constructor to resolve references',
            );
        }

        if ($this->ref === null) {
            throw new UnableToResolveReferences(
                'Unable to resolve LexRef without a ref: ' . json_encode($this, JSON_UNESCAPED_SLASHES),
            );
        }

        $nsid = $this->getNsidForRef();
        $schemaFile = $this->parserFactory->getSchemaRepository()->findSchemaPathByNsid($nsid);

        if ($schemaFile === null) {
            throw new UnableToResolveReferences('Unable to locate schema file for ref: ' . $this->ref);
        }

        $schemaContents = (string) file_get_contents($schemaFile);

        $entity = $this->parserFactory->getParser(LexiconParser::class)->parse($schemaContents);

        /** @psalm-suppress NoInterfaceProperties */
        if (!isset($entity->defs) || !is_array($entity->defs) || !isset($entity->defs[$nsid->defId])) {
            throw new UnableToResolveReferences(sprintf(
                'Def ID "#%s" does not exist in schema for NSID "%s"',
                $nsid->defId,
                $nsid->nsid,
            ));
        }

        /** @var LexEntity */
        return $entity->defs[$nsid->defId];
    }

    private function getNsidForRef(): Nsid
    {
        assert($this->ref !== null);

        $effectiveRef = $this->ref;
        if (str_starts_with($effectiveRef, '#')) {
            $ancestor = $this->resolveAncestry($this);

            if (!$ancestor instanceof LexiconDoc) {
                throw new UnableToResolveReferences('Unable to resolve relative reference: ' . $this->ref);
            }

            $effectiveRef = $ancestor->id->nsid . $effectiveRef;
        }

        try {
            return new Nsid($effectiveRef);
        } catch (InvalidNsid $exception) {
            throw new UnableToResolveReferences(
                message: 'Unable to resolve reference for invalid NSID: ' . $this->ref,
                previous: $exception,
            );
        }
    }
}
