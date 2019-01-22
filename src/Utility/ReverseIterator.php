<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

use Generator;

class ReverseIterator
{
    /**
     * Iterate the array reversely.
     *
     * @param array $array the array
     *
     * @return Generator
     */
    public static function fromArray(array $array): Generator
    {
        for (\end($array); ($key = \key($array)) !== null; \prev($array)) {
            yield [\current($array), $key];
        }
    }
}
