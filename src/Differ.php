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
    /**
     * @var array cached properties and their default values
     */
    private const CACHED_PROPERTIES = [
        'groupedOpcodes' => [],
        'oldNewComparison' => 0,
    ];

    /**
     * @var array array of the options that have been applied for generating the diff
     */
    public $options = [];

    /**
     * @var string[] the "old" sequence to use as the basis for the comparison
     */
    private $old = [];

    /**
     * @var string[] the "new" sequence to generate the changes for
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
     * @var int the result of comparing the old and the new with the spaceship operator
     *          -1 means old < new, 0 means old == new, 1 means old > new
     */
    private $oldNewComparison = 0;

    /**
     * @var array array containing the generated opcodes for the differences between the two items
     */
    private $groupedOpcodes = [];

    /**
     * @var array associative array of the default options available for the Differ class and their default value
     */
    private static $defaultOptions = [
        // show how many neighbor lines
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
     *
     * @return self
     */
    public function setOldNew(array $old, array $new): self
    {
        return $this->setOld($old)->setNew($new);
    }

    /**
     * Set old.
     *
     * @param string[] $old the old
     *
     * @return self
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
     *
     * @return self
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
     *
     * @return self
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
     * Compare the old and the new with the spaceship operator.
     *
     * @return int
     */
    public function getOldNewComparison(): int
    {
        return $this->finalize()->oldNewComparison;
    }

    /**
     * Get the singleton.
     *
     * @return self
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
     * @return array[] array of the grouped opcodes for the generated diff
     */
    public function getGroupedOpcodes(): array
    {
        $this->finalize();

        if (!empty($this->groupedOpcodes)) {
            return $this->groupedOpcodes;
        }

        return $this->groupedOpcodes = $this->sequenceMatcher
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
     *
     * @return self
     */
    private function finalize(): self
    {
        if ($this->isCacheDirty) {
            $this->resetCachedResults();

            $this->oldNewComparison = $this->old <=> $this->new;

            $this->sequenceMatcher
                ->setOptions($this->options)
                ->setSequences($this->old, $this->new);
        }

        return $this;
    }

    /**
     * Reset cached results.
     *
     * @return self
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
