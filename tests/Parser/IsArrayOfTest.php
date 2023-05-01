<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\TestWith;
use SocialWeb\Atproto\Lexicon\Parser\IsArrayOf;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

class IsArrayOfTest extends TestCase
{
    use IsArrayOf;

    #[TestWith([123, false])]
    #[TestWith(['foo', false])]
    #[TestWith([12.3, false])]
    #[TestWith([[1, '2', 3], false])]
    #[TestWith([[1, 2, 3], true])]
    #[TestWith([[1.1, 2.3, 3.2], true])]
    #[TestWith([[1.1, 2, 3.2, 4], true])]
    public function testIsArrayOfNumber(mixed $test, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, $this->isArrayOfNumber($test));
    }

    #[TestWith([123, false])]
    #[TestWith(['foo', false])]
    #[TestWith([12.3, false])]
    #[TestWith([[1, '2', 3], false])]
    #[TestWith([[1, 2, 3], true])]
    #[TestWith([[1.1, 2.3, 3.2], false])]
    #[TestWith([[1.1, 2, 3.2, 4], false])]
    public function testIsArrayOfInteger(mixed $test, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, $this->isArrayOfInteger($test));
    }

    #[TestWith([123, false])]
    #[TestWith(['foo', false])]
    #[TestWith([12.3, false])]
    #[TestWith([[1, '2', 3], false])]
    #[TestWith([[1, 2, 3], false])]
    #[TestWith([[1.1, 2.3, 3.2], false])]
    #[TestWith([[1.1, 2, 3.2, 4], false])]
    #[TestWith([['1', '2', '3'], true])]
    #[TestWith([['foo', 'bar', 'baz'], true])]
    public function testIsArrayOfString(mixed $test, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, $this->isArrayOfString($test));
    }
}
