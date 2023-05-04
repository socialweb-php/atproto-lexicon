<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexRef;

use function is_string;

final class LexRefParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexRef
    {
        /** @var object{ref: string} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type)
                && $data->type === 'ref'
                && isset($data->ref)
                && is_string($data->ref),
        );

        return new LexRef(
            ref: $data->ref,
        );
    }
}
