<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use SocialWeb\Atproto\Lexicon\Types\LexRefUnion;

use function is_bool;
use function is_string;

final class LexRefUnionParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexRefUnion
    {
        /** @var object{type: 'union', description?: string, refs: string[], closed?: bool} $data */
        $data = $this->validate(
            $data,
            fn (object $data): bool => isset($data->type) && $data->type === 'union'
                && (!isset($data->description) || is_string($data->description))
                && isset($data->refs) && $this->isArrayOfString($data->refs)
                && (!isset($data->closed) || is_bool($data->closed)),
        );

        return new LexRefUnion(
            description: $data->description ?? null,
            refs: $data->refs,
            closed: $data->closed ?? null,
        );
    }
}
