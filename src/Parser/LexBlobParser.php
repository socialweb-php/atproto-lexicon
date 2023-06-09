<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_float;
use function is_int;
use function is_string;

/**
 * @phpstan-import-type TLexBlob from LexBlob
 */
class LexBlobParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexBlob
    {
        /** @var TLexBlob $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexBlob(
            description: $data->description ?? null,
            accept: $data->accept ?? null,
            maxSize: $data->maxSize ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Blob->value
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->accept) || $this->isArrayOfString($data->accept))
            && (!isset($data->maxSize) || is_int($data->maxSize) || is_float($data->maxSize));
    }
}
