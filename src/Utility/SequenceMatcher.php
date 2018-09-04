<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Utility;

/**
 * Sequence matcher for Diff.
 */
class SequenceMatcher
{
    const OPCODE_DELETE = 'del';
    const OPCODE_EQUAL = 'eq';
    const OPCODE_INSERT = 'ins';
    const OPCODE_REPLACE = 'rep';

    /**
     * @var null|callable either a string or an array containing a callback function to determine if a line is "junk" or not
     */
    protected $junkCallback;

    /**
     * @var array the first sequence to compare against
     */
    protected $a = [];

    /**
     * @var array the second sequence
     */
    protected $b = [];

    /**
     * @var array Array of characters that are considered junk from the second sequence. Characters are the array key.
     */
    protected $junkDict = [];

    /**
     * @var array array of indices that do not contain junk elements
     */
    protected $b2j = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected static $defaultOptions = [
        'ignoreWhitespace' => false,
        'ignoreCase' => false,
    ];

    /**
     * @var array
     */
    protected $fullBCount = [];

    /**
     * @var array
     */
    protected $matchingBlocks = [];

    /**
     * @var array
     */
    protected $opcodes = [];

    /**
     * The constructor. With the sequences being passed, they'll be set
     * for the sequence matcher and it will perform a basic cleanup &
     * calculate junk elements.
     *
     * @param string[]      $a            an array containing the lines to compare against
     * @param string[]      $b            an array containing the lines to compare
     * @param null|callable $junkCallback either an array or string that references a callback function (if there is one) to determine 'junk' characters
     * @param array         $options      the options
     */
    public function __construct(array $a, array $b, ?callable $junkCallback = null, array $options = [])
    {
        $this->a = [];
        $this->b = [];
        $this->junkCallback = $junkCallback;
        $this->setOptions($options);
        $this->setSequences($a, $b);
    }

    /**
     * Set the options.
     *
     * @param array $options The options
     *
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options + static::$defaultOptions;

        return $this;
    }

    /**
     * Set the first and second sequences to use with the sequence matcher.
     *
     * @param string[] $a an array containing the lines to compare against
     * @param string[] $b an array containing the lines to compare
     *
     * @return self
     */
    public function setSequences(array $a, array $b): self
    {
        $this->setSeq1($a)->setSeq2($b);

        return $this;
    }

    /**
     * Set the first sequence ($a) and reset any internal caches to indicate that
     * when calling the calculation methods, we need to recalculate them.
     *
     * @param string[] $a the sequence to set as the first sequence
     *
     * @return self
     */
    public function setSeq1(array $a): self
    {
        if ($this->a !== $a) {
            $this->a = $a;
            $this->matchingBlocks = [];
            $this->opcodes = [];
        }

        return $this;
    }

    /**
     * Set the second sequence ($b) and reset any internal caches to indicate that
     * when calling the calculation methods, we need to recalculate them.
     *
     * @param string[] $b the sequence to set as the second sequence
     *
     * @return self
     */
    public function setSeq2(array $b): self
    {
        if ($this->b !== $b) {
            $this->b = $b;
            $this->matchingBlocks = [];
            $this->opcodes = [];
            $this->fullBCount = [];
            $this->chainB();
        }

        return $this;
    }

    /**
     * Find the longest matching block in the two sequences, as defined by the
     * lower and upper constraints for each sequence. (for the first sequence,
     * $alo - $ahi and for the second sequence, $blo - $bhi).
     *
     * Essentially, of all of the maximal matching blocks, return the one that
     * startest earliest in $a, and all of those maximal matching blocks that
     * start earliest in $a, return the one that starts earliest in $b.
     *
     * If the junk callback is defined, do the above but with the restriction
     * that the junk element appears in the block. Extend it as far as possible
     * by matching only junk elements in both $a and $b.
     *
     * @param int $alo the lower constraint for the first sequence
     * @param int $ahi the upper constraint for the first sequence
     * @param int $blo the lower constraint for the second sequence
     * @param int $bhi the upper constraint for the second sequence
     *
     * @return array an array containing the longest match that includes the starting position in $a, start in $b and the length/size
     */
    public function findLongestMatch(int $alo, int $ahi, int $blo, int $bhi): array
    {
        $a = $this->a;
        $b = $this->b;

        $bestI = $alo;
        $bestJ = $blo;
        $bestSize = 0;

        $j2Len = [];

        for ($i = $alo; $i < $ahi; ++$i) {
            $newJ2Len = [];
            $jDict = $this->b2j[$a[$i]] ?? [];
            foreach ($jDict as $jKey => $j) {
                if ($j < $blo) {
                    continue;
                }
                if ($j >= $bhi) {
                    break;
                }

                $k = ($j2Len[$j - 1] ?? 0) + 1;
                $newJ2Len[$j] = $k;
                if ($k > $bestSize) {
                    $bestI = $i - $k + 1;
                    $bestJ = $j - $k + 1;
                    $bestSize = $k;
                }
            }

            $j2Len = $newJ2Len;
        }

        while (
            $bestI > $alo &&
            $bestJ > $blo &&
            !$this->isBJunk($b[$bestJ - 1]) &&
            !$this->linesAreDifferent($bestI - 1, $bestJ - 1)
        ) {
            --$bestI;
            --$bestJ;
            ++$bestSize;
        }

        while (
            $bestI + $bestSize < $ahi &&
            ($bestJ + $bestSize) < $bhi &&
            !$this->isBJunk($b[$bestJ + $bestSize]) &&
            !$this->linesAreDifferent($bestI + $bestSize, $bestJ + $bestSize)
        ) {
            ++$bestSize;
        }

        while (
            $bestI > $alo &&
            $bestJ > $blo &&
            $this->isBJunk($b[$bestJ - 1]) &&
            !$this->linesAreDifferent($bestI - 1, $bestJ - 1)
        ) {
            --$bestI;
            --$bestJ;
            ++$bestSize;
        }

        while (
            $bestI + $bestSize < $ahi &&
            $bestJ + $bestSize < $bhi &&
            $this->isBJunk($b[$bestJ + $bestSize]) &&
            !$this->linesAreDifferent($bestI + $bestSize, $bestJ + $bestSize)
        ) {
            ++$bestSize;
        }

        return [$bestI, $bestJ, $bestSize];
    }

    /**
     * Check if the two lines at the given indexes are different or not.
     *
     * @param int $aIndex line number to check against in a
     * @param int $bIndex line number to check against in b
     *
     * @return bool true if the lines are different and false if not
     */
    public function linesAreDifferent(int $aIndex, int $bIndex): bool
    {
        $lineA = $this->a[$aIndex];
        $lineB = $this->b[$bIndex];

        if ($this->options['ignoreWhitespace']) {
            static $replace = ["\t", ' '];

            $lineA = \str_replace($replace, '', $lineA);
            $lineB = \str_replace($replace, '', $lineB);
        }

        if ($this->options['ignoreCase']) {
            $lineA = \strtolower($lineA);
            $lineB = \strtolower($lineB);
        }

        return $lineA !== $lineB;
    }

    /**
     * Return a nested set of arrays for all of the matching sub-sequences
     * in the strings $a and $b.
     *
     * Each block contains the lower constraint of the block in $a, the lower
     * constraint of the block in $b and finally the number of lines that the
     * block continues for.
     *
     * @return array a nested array of the matching blocks, as described by the function
     */
    public function getMatchingBlocks(): array
    {
        if (!empty($this->matchingBlocks)) {
            return $this->matchingBlocks;
        }

        $aCount = \count($this->a);
        $bCount = \count($this->b);

        $queue = [
            [0, $aCount, 0, $bCount],
        ];

        $matchingBlocks = [];
        while (!empty($queue)) {
            [$alo, $ahi, $blo, $bhi] = \array_pop($queue);
            $x = $this->findLongestMatch($alo, $ahi, $blo, $bhi);
            [$i, $j, $k] = $x;
            if ($k) {
                $matchingBlocks[] = $x;
                if ($alo < $i && $blo < $j) {
                    $queue[] = [$alo, $i, $blo, $j];
                }

                if ($i + $k < $ahi && $j + $k < $bhi) {
                    $queue[] = [$i + $k, $ahi, $j + $k, $bhi];
                }
            }
        }

        \usort($matchingBlocks, function (array $a, array $b): int {
            $aCount = \count($a);
            $bCount = \count($b);
            $max = \max($aCount, $bCount);

            for ($i = 0; $i < $max; ++$i) {
                if ($a[$i] !== $b[$i]) {
                    return $a[$i] <=> $b[$i];
                }
            }

            return $aCount <=> $bCount;
        });

        $i1 = $j1 = $k1 = 0;
        $nonAdjacent = [];
        foreach ($matchingBlocks as $block) {
            [$i2, $j2, $k2] = $block;
            if ($i1 + $k1 === $i2 && $j1 + $k1 === $j2) {
                $k1 += $k2;
            } else {
                if ($k1) {
                    $nonAdjacent[] = [$i1, $j1, $k1];
                }

                $i1 = $i2;
                $j1 = $j2;
                $k1 = $k2;
            }
        }

        if ($k1) {
            $nonAdjacent[] = [$i1, $j1, $k1];
        }

        $nonAdjacent[] = [$aCount, $bCount, 0];

        $this->matchingBlocks = $nonAdjacent;

        return $this->matchingBlocks;
    }

    /**
     * Return a list of all of the opcodes for the differences between the
     * two strings.
     *
     * The nested array returned contains an array describing the opcode
     * which includes:
     * 0 - The type of tag (as described below) for the opcode.
     * 1 - The beginning line in the first sequence.
     * 2 - The end line in the first sequence.
     * 3 - The beginning line in the second sequence.
     * 4 - The end line in the second sequence.
     *
     * The different types of tags include:
     * replace - The string from $i1 to $i2 in $a should be replaced by
     *           the string in $b from $j1 to $j2.
     * delete -  The string in $a from $i1 to $j2 should be deleted.
     * insert -  The string in $b from $j1 to $j2 should be inserted at
     *           $i1 in $a.
     * equal  -  The two strings with the specified ranges are equal.
     *
     * @return array array of the opcodes describing the differences between the strings
     */
    public function getOpcodes(): array
    {
        if (!empty($this->opcodes)) {
            return $this->opcodes;
        }

        $i = $j = 0;
        $this->opcodes = [];

        $blocks = $this->getMatchingBlocks();
        foreach ($blocks as $block) {
            [$ai, $bj, $size] = $block;
            if ($i < $ai && $j < $bj) {
                $tag = static::OPCODE_REPLACE;
            } elseif ($i < $ai) {
                $tag = static::OPCODE_DELETE;
            } elseif ($j < $bj) {
                $tag = static::OPCODE_INSERT;
            } else {
                $tag = null;
            }

            if ($tag) {
                $this->opcodes[] = [$tag, $i, $ai, $j, $bj];
            }

            $i = $ai + $size;
            $j = $bj + $size;

            if ($size) {
                $this->opcodes[] = [static::OPCODE_EQUAL, $ai, $i, $bj, $j];
            }
        }

        return $this->opcodes;
    }

    /**
     * Return a series of nested arrays containing different groups of generated
     * opcodes for the differences between the strings with up to $context lines
     * of surrounding content.
     *
     * Essentially what happens here is any big equal blocks of strings are stripped
     * out, the smaller subsets of changes are then arranged in to their groups.
     * This means that the sequence matcher and diffs do not need to include the full
     * content of the different files but can still provide context as to where the
     * changes are.
     *
     * @param int $context the number of lines of context to provide around the groups
     *
     * @return array nested array of all of the grouped opcodes
     */
    public function getGroupedOpcodes(int $context = 3): array
    {
        $opcodes = $this->getOpcodes();
        if (empty($opcodes)) {
            $opcodes = [
                [static::OPCODE_EQUAL, 0, 1, 0, 1],
            ];
        }

        if ($opcodes[0][0] === static::OPCODE_EQUAL) {
            $opcodes[0] = [
                $opcodes[0][0],
                \max($opcodes[0][1], $opcodes[0][2] - $context),
                $opcodes[0][2],
                \max($opcodes[0][3], $opcodes[0][4] - $context),
                $opcodes[0][4],
            ];
        }

        $lastItem = \count($opcodes) - 1;
        if ($opcodes[$lastItem][0] === static::OPCODE_EQUAL) {
            [$tag, $i1, $i2, $j1, $j2] = $opcodes[$lastItem];
            $opcodes[$lastItem] = [
                $tag,
                $i1,
                \min($i2, $i1 + $context),
                $j1,
                \min($j2, $j1 + $context),
            ];
        }

        $maxRange = $context * 2;
        $groups = $group = [];
        foreach ($opcodes as $code) {
            [$tag, $i1, $i2, $j1, $j2] = $code;
            if ($tag === static::OPCODE_EQUAL && $i2 - $i1 > $maxRange) {
                $group[] = [
                    $tag,
                    $i1,
                    \min($i2, $i1 + $context),
                    $j1,
                    \min($j2, $j1 + $context),
                ];
                $groups[] = $group;
                $group = [];
                $i1 = \max($i1, $i2 - $context);
                $j1 = \max($j1, $j2 - $context);
            }
            $group[] = [$tag, $i1, $i2, $j1, $j2];
        }

        if (
            !empty($group) &&
            !(\count($group) === 1 && $group[0][0] === static::OPCODE_EQUAL)
        ) {
            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * Return a measure of the similarity between the two sequences.
     * This will be a float value between 0 and 1.
     *
     * Out of all of the ratio calculation functions, this is the most
     * expensive to call if getMatchingBlocks or getOpcodes is yet to be
     * called. The other calculation methods (quickRatio and realQuickRatio)
     * can be used to perform quicker calculations but may be less accurate.
     *
     * The ratio is calculated as (2 * number of matches) / total number of
     * elements in both sequences.
     *
     * @return float the calculated ratio
     */
    public function ratio(): float
    {
        $matchesCount = \array_reduce(
            $this->getMatchingBlocks(),
            function (int $sum, array $triple): int {
                return $sum + $triple[\count($triple) - 1];
            },
            0
        );

        return $this->calculateRatio($matchesCount, \count($this->a) + \count($this->b));
    }

    /**
     * Generate the internal arrays containing the list of junk and non-junk
     * characters for the second ($b) sequence.
     *
     * @return self
     */
    protected function chainB(): self
    {
        $length = \count($this->b);
        $this->b2j = [];
        $popularDict = [];

        for ($i = 0; $i < $length; ++$i) {
            $char = $this->b[$i];
            if (isset($this->b2j[$char])) {
                if ($length >= 200 && \count($this->b2j[$char]) * 100 > $length) {
                    $popularDict[$char] = 1;
                    unset($this->b2j[$char]);
                } else {
                    $this->b2j[$char][] = $i;
                }
            } else {
                $this->b2j[$char] = [$i];
            }
        }

        // remove leftovers
        foreach (\array_keys($popularDict) as $char) {
            unset($this->b2j[$char]);
        }

        $this->junkDict = [];
        if (\is_callable($this->junkCallback)) {
            foreach (\array_keys($popularDict) as $char) {
                if (($this->junkCallback)($char)) {
                    $this->junkDict[$char] = 1;
                    unset($popularDict[$char]);
                }
            }

            foreach (\array_keys($this->b2j) as $char) {
                if (($this->junkCallback)($char)) {
                    $this->junkDict[$char] = 1;
                    unset($this->b2j[$char]);
                }
            }
        }

        return $this;
    }

    /**
     * Checks if a particular character is in the junk dictionary
     * for the list of junk characters.
     *
     * @param string $b
     *
     * @return bool $b True if the character is considered junk. False if not.
     */
    protected function isBJunk(string $b): bool
    {
        return isset($this->junkDict[$b]);
    }

    /**
     * Quickly return an upper bound ratio for the similarity of the strings.
     * This is quicker to compute than ratio().
     *
     * @return float the calculated ratio
     */
    protected function quickRatio(): float
    {
        $aCount = \count($this->a);
        $bCount = \count($this->b);

        if (empty($this->fullBCount)) {
            for ($i = 0; $i < $bCount; ++$i) {
                $char = $this->b[$i];
                $this->fullBCount[$char] = ($this->fullBCount[$char] ?? 0) + 1;
            }
        }

        $avail = [];
        $matchesCount = 0;
        for ($i = 0; $i < $aCount; ++$i) {
            $char = $this->a[$i];
            $numb = $avail[$char] ?? ($this->fullBCount[$char] ?? 0);
            $avail[$char] = $numb - 1;
            if ($numb > 0) {
                ++$matchesCount;
            }
        }

        return $this->calculateRatio($matchesCount, $aCount + $bCount);
    }

    /**
     * Return an upper bound ratio really quickly for the similarity of the strings.
     * This is quicker to compute than ratio() and quickRatio().
     *
     * @return float the calculated ratio
     */
    protected function realQuickRatio(): float
    {
        $aCount = \count($this->a);
        $bCount = \count($this->b);

        return $this->calculateRatio(\min($aCount, $bCount), $aCount + $bCount);
    }

    /**
     * Helper function for calculating the ratio to measure similarity for the strings.
     * The ratio is defined as being 2 * (number of matches / total length).
     *
     * @param int $matchesCount the number of matches in the two strings
     * @param int $length       the length of the two strings
     *
     * @return float the calculated ratio
     */
    protected function calculateRatio(int $matchesCount, int $length = 0): float
    {
        return $length ? ($matchesCount << 1) / $length : 1;
    }
}
