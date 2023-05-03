<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexUnknown;

use function is_string;

final class LexUnknownParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexUnknown
    {
        /** @var object{description?: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'unknown'
                && (!isset($data->description) || is_string($data->description)),
        );

        return new LexUnknown(
            description: $data->description ?? null,
        );
    }
}
