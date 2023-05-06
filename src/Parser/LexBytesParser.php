<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexBytes;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_int;
use function is_string;

/**
 * @phpstan-import-type TLexBytes from LexBytes
 */
class LexBytesParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexBytes
    {
        /** @var TLexBytes $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexBytes(
            description: $data->description ?? null,
            maxLength: $data->maxLength ?? null,
            minLength: $data->minLength ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Bytes->value
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->maxLength) || is_int($data->maxLength))
            && (!isset($data->minLength) || is_int($data->minLength));
    }
}
