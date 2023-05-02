<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexImage;

use function is_float;
use function is_int;
use function is_string;

final class LexImageParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexImage
    {
        /** @var object{accept?: string[], maxSize?: float | int, maxWidth?: float | int, maxHeight?: float | int, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'image'
                && (!isset($data->accept) || $this->isArrayOfString($data->accept))
                && (!isset($data->maxSize) || is_int($data->maxSize) || is_float($data->maxSize))
                && (!isset($data->maxWidth) || is_int($data->maxWidth) || is_float($data->maxWidth))
                && (!isset($data->maxHeight) || is_int($data->maxHeight) || is_float($data->maxHeight))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexImage(
            accept: $data->accept ?? null,
            maxSize: $data->maxSize ?? null,
            maxWidth: $data->maxWidth ?? null,
            maxHeight: $data->maxHeight ?? null,
            description: $data->description ?? null,
        );
    }
}
