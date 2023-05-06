<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexCidLink;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_string;

/**
 * @phpstan-import-type TLexCidLink from LexCidLink
 */
class LexCidLinkParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexCidLink
    {
        /** @var TLexCidLink $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexCidLink(
            description: $data->description ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::CidLink->value
            && (!isset($data->description) || is_string($data->description));
    }
}
