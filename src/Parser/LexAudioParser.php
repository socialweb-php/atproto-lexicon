<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexAudio;

use function is_float;
use function is_int;
use function is_string;

final class LexAudioParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexAudio
    {
        /** @var object{accept?: string[], maxSize?: float | int, maxLength?: float | int, description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'audio'
                && (!isset($data->accept) || $this->isArrayOfString($data->accept))
                && (!isset($data->maxSize) || is_int($data->maxSize) || is_float($data->maxSize))
                && (!isset($data->maxLength) || is_int($data->maxLength) || is_float($data->maxLength))
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexAudio(
            accept: $data->accept ?? null,
            maxSize: $data->maxSize ?? null,
            maxLength: $data->maxLength ?? null,
            description: $data->description ?? null,
        );
    }
}
