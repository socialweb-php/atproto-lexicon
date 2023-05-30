<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators\Formats;

use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Atproto\Lexicon\Validators\Validator;

use function explode;
use function in_array;
use function is_string;
use function preg_match;
use function preg_split;
use function str_contains;
use function str_ends_with;
use function str_replace;

/**
 * Validation for datetime formatted strings.
 *
 * Many thanks to Volodymyr Yepishev and contributors to the
 * iso-datestring-validator JavaScript library, from which this class
 * was ported.
 *
 * iso-datestring-validator is licensed under the MIT License and is
 * Copyright (c) 2019 Volodymyr Yepishev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @link https://github.com/Bwca/package_iso-datestring-validator iso-datestring-validator on GitHub
 * @link https://www.npmjs.com/package/iso-datestring-validator iso-datestring-validator on NPM
 */
class DatetimeValidator implements Validator
{
    private const SEPARATORS_TO_ESCAPE = ['\\', '^', '$', '.', '|', '?', '*', '+', '(', ')', '[', ']', '{', '}'];

    /**
     * @throws InvalidValue
     */
    public function validate(mixed $value, ?string $path = null): string
    {
        $path = $path ?? 'Value';

        if (!is_string($value) || !$this->isValidIsoDateString($value)) {
            throw new InvalidValue("$path must be an ISO 8601 formatted datetime string");
        }

        return $value;
    }

    private function isValidIsoDateString(string $dateString): bool
    {
        $dateAndTimeWithOffset = explode('T', $dateString);
        $date = $dateAndTimeWithOffset[0] ?? '';
        $timeWithOffset = $dateAndTimeWithOffset[1] ?? '';

        $dateSeparator = $this->getStringSeparator($date);
        $isDateValid = $this->isValidDate($date, $dateSeparator);

        if (!$timeWithOffset) {
            return false;
        }

        $timeSeparator = $this->getTimeStringSeparator($timeWithOffset);

        return $isDateValid && $this->isValidTime($timeWithOffset, $timeSeparator, true);
    }

    private function getStringSeparator(string $dateString): string
    {
        if (preg_match('/\D/', $dateString, $matches)) {
            return $matches[0];
        }

        return '';
    }

    private function isValidDate(string $date, string $s = '-'): bool
    {
        if (in_array($s, self::SEPARATORS_TO_ESCAPE)) {
            $s = "\\$s";
        }

        $pattern = "/^(?!0{4}{$s}0{2}{$s}0{2})((?=[0-9]{4}{$s}(((0[^2])|1[0-2])|02(?={$s}(([0-1][0-9])|2[0-8]))){$s}[0-9]{2})|(?=((([13579][26])|([2468][048])|(0[48]))0{2})|([0-9]{2}((((0|[2468])[48])|[2468][048])|([13579][26]))){$s}02{$s}29))([0-9]{4}){$s}(?!((0[469])|11){$s}31)((0[1,3-9]|1[0-2])|(02(?!{$s}3))){$s}(0[1-9]|[1-2][0-9]|3[0-1])$/"; // phpcs:ignore Generic.Files.LineLength.TooLong

        return (bool) preg_match($pattern, $date);
    }

    private function getTimeStringSeparator(string $timeString): string
    {
        if (preg_match('/([^Z+\-\d])(?=\d+\1)/', $timeString, $matches)) {
            return $matches[0];
        }

        return '';
    }

    private function isValidTime(string $timeWithOffset, string $s = ':', bool $isTimezoneCheckOn = false): bool
    {
        $pattern = "/^([0-1]|2(?=([0-3])|4{$s}00))[0-9]{$s}[0-5][0-9]({$s}([0-5]|6(?=0))[0-9])?(\.[0-9]{1,9})?$/";

        if (!$isTimezoneCheckOn || !preg_match('/[Z+\-]/', $timeWithOffset)) {
            return (bool) preg_match($pattern, $timeWithOffset);
        }

        if (str_ends_with($timeWithOffset, 'Z')) {
            return (bool) preg_match($pattern, str_replace('Z', '', $timeWithOffset));
        }

        $isPositiveTimezoneOffset = str_contains($timeWithOffset, '+');

        $timeAndOffset = preg_split('/[+-]/', $timeWithOffset);
        $time = $timeAndOffset[0] ?? '';
        $offset = $timeAndOffset[1] ?? '';

        return preg_match($pattern, $time)
            && $this->isValidZoneOffset($offset, $isPositiveTimezoneOffset, $this->getStringSeparator($offset));
    }

    private function isValidZoneOffset(string $offset, bool $isPositiveOffset, string $s = ':'): bool
    {
        $pattern = $isPositiveOffset
            ? "/^(0(?!(2{$s}4)|0{$s}3)|1(?=([0-1]|2(?={$s}[04])|[34](?={$s}0))))([03469](?={$s}[03])|[17](?={$s}0)|2(?={$s}[04])|5(?={$s}[034])|8(?={$s}[04])){$s}([03](?=0)|4(?=5))[05]$/" // phpcs:ignore Generic.Files.LineLength.TooLong
            : "/^(0(?=[^0])|1(?=[0-2]))([39](?={$s}[03])|[0-24-8](?={$s}00)){$s}[03]0$/";

        return (bool) preg_match($pattern, $offset);
    }
}
