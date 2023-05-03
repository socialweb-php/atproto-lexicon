<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexVideo;

use function is_float;
use function is_int;
use function is_string;

final class LexVideoParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexVideo
    {
        /** @var object{accept?: string[], maxSize?: float | int, maxWidth?: float | int, maxHeight?: float | int, maxLength?: float | int, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'video'
                && (!isset($data->accept) || $this->isArrayOfString($data->accept))
                && (!isset($data->maxSize) || is_int($data->maxSize) || is_float($data->maxSize))
                && (!isset($data->maxWidth) || is_int($data->maxWidth) || is_float($data->maxWidth))
                && (!isset($data->maxHeight) || is_int($data->maxHeight) || is_float($data->maxHeight))
                && (!isset($data->maxLength) || is_int($data->maxLength) || is_float($data->maxLength))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexVideo(
            accept: $data->accept ?? null,
            maxSize: $data->maxSize ?? null,
            maxWidth: $data->maxWidth ?? null,
            maxHeight: $data->maxHeight ?? null,
            maxLength: $data->maxLength ?? null,
            description: $data->description ?? null,
        );
    }
}
