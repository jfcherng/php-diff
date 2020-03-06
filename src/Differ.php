<?php

declare(strict_types=1);

namespace Jfcherng\Diff;

/**
 * A comprehensive library for generating differences between two strings
 * in multiple formats (unified, side by side HTML etc).
 *
 * @author Jack Cherng <jfcherng@gmail.com>
 * @author Chris Boulton <chris.boulton@interspire.com>
 *
 * @see http://github.com/chrisboulton/php-diff
 */
final class Differ
{
    /** @var int a safe number for indicating showing all contexts */
    const CONTEXT_ALL = \PHP_INT_MAX >> 3;

    /**
     * @var array cached properties and their default values
     */
    private const CACHED_PROPERTIES = [
        'groupedOpcodes' => [],
        'groupedOpcodesGnu' => [],
        'oldNoEolAtEofIdx' => -1,
        'newNoEolAtEofIdx' => -1,
        'oldNewComparison' => 0,
    ];

    /**
     * @var array array of the options that have been applied for generating the diff
     */
    public $options = [];

    /**
     * @var string[] the old sequence
     */
    private $old = [];

    /**
     * @var string[] the new sequence
     */
    private $new = [];

    /**
     * @var bool is any of cached properties dirty?
     */
    private $isCacheDirty = true;

    /**
     * @var SequenceMatcher the sequence matcher
     */
    private $sequenceMatcher;

    /**
     * @var int the end index for the old if the old has no EOL at EOF
     *          -1 means the old has an EOL at EOF
     */
    private $oldNoEolAtEofIdx = -1;

    /**
     * @var int the end index for the new if the new has no EOL at EOF
     *          -1 means the new has an EOL at EOF
     */
    private $newNoEolAtEofIdx = -1;

    /**
     * @var int the result of comparing the old and the new with the spaceship operator
     *          -1 means old < new, 0 means old == new, 1 means old > new
     */
    private $oldNewComparison = 0;

    /**
     * @var int[][][] array containing the generated opcodes for the differences between the two items
     */
    private $groupedOpcodes = [];

    /**
     * @var int[][][] array containing the generated opcodes for the differences between the two items (GNU version)
     */
    private $groupedOpcodesGnu = [];

    /**
     * @var array associative array of the default options available for the Differ class and their default value
     */
    private static $defaultOptions = [
        // show how many neighbor lines
        // Differ::CONTEXT_ALL can be used to show the whole file
        'context' => 3,
        // ignore case difference
        'ignoreWhitespace' => false,
        // ignore whitespace difference
        'ignoreCase' => false,
    ];

    /**
     * The constructor.
     *
     * @param string[] $old     array containing the lines of the old string to compare
     * @param string[] $new     array containing the lines for the new string to compare
     * @param array    $options the options
     */
    public function __construct(array $old, array $new, array $options = [])
    {
        $this->sequenceMatcher = new SequenceMatcher([], []);

        $this->setOldNew($old, $new)->setOptions($options);
    }

    /**
     * Set old and new.
     *
     * @param string[] $old the old
     * @param string[] $new the new
     */
    public function setOldNew(array $old, array $new): self
    {
        return $this->setOld($old)->setNew($new);
    }

    /**
     * Set old.
     *
     * @param string[] $old the old
     */
    public function setOld(array $old): self
    {
        if ($this->old !== $old) {
            $this->old = $old;
            $this->isCacheDirty = true;
        }

        return $this;
    }

    /**
     * Set new.
     *
     * @param string[] $new the new
     */
    public function setNew(array $new): self
    {
        if ($this->new !== $new) {
            $this->new = $new;
            $this->isCacheDirty = true;
        }

        return $this;
    }

    /**
     * Set the options.
     *
     * @param array $options the options
     */
    public function setOptions(array $options): self
    {
        $mergedOptions = $options + static::$defaultOptions;

        if ($this->options !== $mergedOptions) {
            $this->options = $mergedOptions;
            $this->isCacheDirty = true;
        }

        return $this;
    }

    /**
     * Get a range of lines from $start to $end from the old string and return them as an array.
     *
     * If $end is null, it returns array sliced from the $start to the end.
     *
     * @param int      $start the starting number. If null, the whole array will be returned.
     * @param null|int $end   the ending number. If null, only the item in $start will be returned.
     *
     * @return string[] array of all of the lines between the specified range
     */
    public function getOld(int $start = 0, ?int $end = null): array
    {
        return $this->getText($this->old, $start, $end);
    }

    /**
     * Get a range of lines from $start to $end from the new string and return them as an array.
     *
     * If $end is null, it returns array sliced from the $start to the end.
     *
     * @param int      $start the starting number
     * @param null|int $end   the ending number
     *
     * @return string[] array of all of the lines between the specified range
     */
    public function getNew(int $start = 0, ?int $end = null): array
    {
        return $this->getText($this->new, $start, $end);
    }

    /**
     * Get the options.
     *
     * @return array the options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get the old no EOL at EOF index.
     *
     * @return int the old no EOL at EOF index
     */
    public function getOldNoEolAtEofIdx(): int
    {
        return $this->finalize()->oldNoEolAtEofIdx;
    }

    /**
     * Get the new no EOL at EOF index.
     *
     * @return int the new no EOL at EOF index
     */
    public function getNewNoEolAtEofIdx(): int
    {
        return $this->finalize()->newNoEolAtEofIdx;
    }

    /**
     * Compare the old and the new with the spaceship operator.
     */
    public function getOldNewComparison(): int
    {
        return $this->finalize()->oldNewComparison;
    }

    /**
     * Get the singleton.
     */
    public static function getInstance(): self
    {
        static $singleton;

        return $singleton = $singleton ?? new static([], []);
    }

    /**
     * Generate a list of the compiled and grouped opcodes for the differences between the
     * two strings. Generally called by the renderer, this class instantiates the sequence
     * matcher and performs the actual diff generation and return an array of the opcodes
     * for it. Once generated, the results are cached in the Differ class instance.
     *
     * @return int[][][] array of the grouped opcodes for the generated diff
     */
    public function getGroupedOpcodes(): array
    {
        $this->finalize();

        if (!empty($this->groupedOpcodes)) {
            return $this->groupedOpcodes;
        }

        return $this->groupedOpcodes = $this->sequenceMatcher
            ->setSequences($this->old, $this->new)
            ->getGroupedOpcodes($this->options['context']);
    }

    /**
     * A EOL-at-EOF-sensitve version of getGroupedOpcodes().
     *
     * @return int[][][] array of the grouped opcodes for the generated diff (GNU version)
     */
    public function getGroupedOpcodesGnu(): array
    {
        $this->finalize();

        if (!empty($this->groupedOpcodesGnu)) {
            return $this->groupedOpcodesGnu;
        }

        $old = \array_map(
            function (string $line): string {
                return "{$line}\n";
            },
            $this->old
        );

        $new = \array_map(
            function (string $line): string {
                return "{$line}\n";
            },
            $this->new
        );

        // note that the old and the new are never empty at this point
        // they have at least one element "\n" in the array because explode("\n", "") === [""]
        $oldLastIdx = \count($old) - 1;
        $newLastIdx = \count($new) - 1;
        $oldLast = &$old[$oldLastIdx];
        $newLast = &$new[$newLastIdx];

        if ($oldLast === "\n") {
            // remove the last plain "\n" line since we don't need it anymore
            unset($old[$oldLastIdx]);
        } else {
            // this means the original source has no EOL at EOF
            // we have to remove the trailing \n from the last line
            $oldLast = \substr($oldLast, 0, -1);
        }

        if ($newLast === "\n") {
            unset($new[$newLastIdx]);
        } else {
            $newLast = \substr($newLast, 0, -1);
        }

        return $this->groupedOpcodesGnu = $this->sequenceMatcher
            ->setSequences($old, $new)
            ->getGroupedOpcodes($this->options['context']);
    }

    /**
     * Claim this class has settled down and we could calculate cached
     * properties by current properties.
     *
     * This method must be called before accessing cached properties to
     * make suer that you will not get a outdated cached value.
     *
     * @internal
     */
    private function finalize(): self
    {
        if ($this->isCacheDirty) {
            $this->resetCachedResults();

            $this->oldNoEolAtEofIdx = $this->getOld(-1) === [''] ? -1 : \count($this->old);
            $this->newNoEolAtEofIdx = $this->getNew(-1) === [''] ? -1 : \count($this->new);
            $this->oldNewComparison = $this->old <=> $this->new;

            $this->sequenceMatcher->setOptions($this->options);
        }

        return $this;
    }

    /**
     * Reset cached results.
     */
    private function resetCachedResults(): self
    {
        foreach (static::CACHED_PROPERTIES as $property => $value) {
            $this->{$property} = $value;
        }

        $this->isCacheDirty = false;

        return $this;
    }

    /**
     * The work horse of getOld() and getNew().
     *
     * If $end is null, it returns array sliced from the $start to the end.
     *
     * @param string[] $lines the array of lines
     * @param int      $start the starting number
     * @param null|int $end   the ending number
     *
     * @return string[] array of all of the lines between the specified range
     */
    private function getText(array $lines, int $start = 0, ?int $end = null): array
    {
        $arrayLength = \count($lines);

        // make $end set
        $end = $end ?? $arrayLength;

        // make $start non-negative
        if ($start < 0) {
            $start += $arrayLength;

            if ($start < 0) {
                $start = 0;
            }
        }

        // may prevent from calling array_slice()
        if ($start === 0 && $end >= $arrayLength) {
            return $lines;
        }

        // make $end non-negative
        if ($end < 0) {
            $end += $arrayLength;

            if ($end < 0) {
                $end = 0;
            }
        }

        // now both $start and $end are non-negative
        // hence the length for array_slice() must be non-negative
        return \array_slice($lines, $start, \max(0, $end - $start));
    }
}
