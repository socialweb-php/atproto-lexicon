<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexBlob;

use function is_float;
use function is_int;
use function is_string;

final class LexBlobParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexBlob
    {
        /** @var object{accept?: string[], maxSize?: float | int, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'blob'
                && (!isset($data->accept) || $this->isArrayOfString($data->accept))
                && (!isset($data->maxSize) || is_int($data->maxSize) || is_float($data->maxSize))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexBlob(
            accept: $data->accept ?? null,
            maxSize: $data->maxSize ?? null,
            description: $data->description ?? null,
        );
    }
}
