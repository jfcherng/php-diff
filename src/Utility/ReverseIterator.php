<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

final class ReverseIterator
{
    const ITERATOR_GET_KEY = 1 << 1;
    const ITERATOR_GET_BOTH = 1 << 2;

    /**
     * The constructor.
     */
    private function __construct()
    {
    }

    /**
     * Iterate the array reversely.
     *
     * @param array $array the array
     *
     * @return \Generator
     */
    public static function fromArray(array $array, int $flags = 0): \Generator
    {
        // it may worth unrolling if-conditions to be out of for-loop
        // so it does have to check if-conditions in each iteration

        if ($flags & self::ITERATOR_GET_BOTH) {
            for (\end($array); ($key = \key($array)) !== null; \prev($array)) {
                yield $key => \current($array);
            }

            return;
        }

        if ($flags & self::ITERATOR_GET_KEY) {
            for (\end($array); ($key = \key($array)) !== null; \prev($array)) {
                yield $key;
            }

            return;
        }

        for (\end($array); \key($array) !== null; \prev($array)) {
            yield \current($array);
        }
    }
}
