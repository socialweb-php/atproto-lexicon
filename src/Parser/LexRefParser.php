<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexRef;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_string;

/**
 * @phpstan-import-type TLexRef from LexRef
 */
class LexRefParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexRef
    {
        /** @var TLexRef $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexRef(
            description: $data->description ?? null,
            ref: $data->ref,
            parserFactory: $this->getParserFactory(),
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Ref->value
            && (!isset($data->description) || is_string($data->description))
            && isset($data->ref) && is_string($data->ref);
    }
}
