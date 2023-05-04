<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitiveType;
use SocialWeb\Atproto\Lexicon\Types\LexRef;

use function is_string;

final class LexRefParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexRef
    {
        /** @var object{ref: string} $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexRef(
            ref: $data->ref,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexPrimitiveType::Ref->value
            && isset($data->ref) && is_string($data->ref);
    }
}
