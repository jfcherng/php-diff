<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

use Generator;

class ReverseIterator
{
    const ITERATOR_GET_KEY = 1 << 1;
    const ITERATOR_GET_BOTH = 1 << 2;

    /**
     * Iterate the array reversely.
     *
     * @param array $array the array
     *
     * @return Generator
     */
    public static function fromArray(array $array, int $flags = 0): Generator
    {
        for (\end($array); ($key = \key($array)) !== null; \prev($array)) {
            $value = \current($array);

            if ($flags & self::ITERATOR_GET_BOTH) {
                yield [$value, $key];

                continue;
            }

            yield $flags & self::ITERATOR_GET_KEY ? $key : $value;
        }
    }
}
