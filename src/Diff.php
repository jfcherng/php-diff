<?php

declare(strict_types=1);

namespace Jfcherng\Diff;

use Jfcherng\Diff\Renderer\AbstractRenderer;

/**
 * A comprehensive library for generating differences between two strings
 * in multiple formats (unified, side by side HTML etc).
 *
 * @author Jack Cherng <jfcherng@gmail.com>
 * @author Chris Boulton <chris.boulton@interspire.com>
 *
 * @see http://github.com/chrisboulton/php-diff
 */
final class Diff
{
    /**
     * @var array array of the options that have been applied for generating the diff
     */
    public $options = [];

    /**
     * @var string[] the "old" sequence to use as the basis for the comparison
     */
    private $a = [];

    /**
     * @var string[] the "new" sequence to generate the changes for
     */
    private $b = [];

    /**
     * @var null|SequenceMatcher the sequence matcher
     */
    private $sequenceMatcher;

    /**
     * @var null|array array containing the generated opcodes for the differences between the two items
     */
    private $groupedCodes;

    /**
     * @var array associative array of the default options available for the diff class and their default value
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
     * @param string[] $a       array containing the lines of the first string to compare
     * @param string[] $b       array containing the lines for the second string to compare
     * @param array    $options
     */
    public function __construct(array $a, array $b, array $options = [])
    {
        $this->sequenceMatcher = new SequenceMatcher([], []);

        $this->setAB($a, $b)->setOptions($options);
    }

    /**
     * Set a and b.
     *
     * @param string[] $a the a
     * @param string[] $b the b
     *
     * @return self
     */
    public function setAB(array $a, array $b): self
    {
        $this->setA($a)->setB($b);

        return $this;
    }

    /**
     * Set a.
     *
     * @param string[] $a the a
     *
     * @return self
     */
    public function setA(array $a): self
    {
        if ($this->a !== $a) {
            $this->a = $a;
            $this->groupedCodes = null;
            $this->sequenceMatcher->setSeq1($a);
        }

        return $this;
    }

    /**
     * Set b.
     *
     * @param string[] $b the b
     *
     * @return self
     */
    public function setB(array $b): self
    {
        if ($this->b !== $b) {
            $this->b = $b;
            $this->groupedCodes = null;
            $this->sequenceMatcher->setSeq2($b);
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
        $this->options = $options + static::$defaultOptions;

        $this->sequenceMatcher->setOptions($this->options);

        return $this;
    }

    /**
     * Get a range of lines from $start to $end from the first comparison string
     * and return them as an array.
     *
     * If $end is null, it returns array sliced from the $start to the end.
     *
     * @param int      $start the starting number. If null, the whole array will be returned.
     * @param null|int $end   the ending number. If null, only the item in $start will be returned.
     *
     * @return string[] array of all of the lines between the specified range
     */
    public function getA(int $start = 0, ?int $end = null): array
    {
        return $this->getText($this->a, $start, $end);
    }

    /**
     * Get a range of lines from $start to $end from the second comparison string
     * and return them as an array.
     *
     * If $end is null, it returns array sliced from the $start to the end.
     *
     * @param int      $start the starting number
     * @param null|int $end   the ending number
     *
     * @return string[] array of all of the lines between the specified range
     */
    public function getB(int $start = 0, ?int $end = null): array
    {
        return $this->getText($this->b, $start, $end);
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
     * for it. Once generated, the results are cached in the diff class instance.
     *
     * @return array[] array of the grouped opcodes for the generated diff
     */
    public function getGroupedOpcodes(): array
    {
        return $this->groupedCodes = $this->groupedCodes ??
            $this->sequenceMatcher->getGroupedOpcodes($this->options['context']);
    }

    /**
     * Render a diff using the supplied rendering class and return it.
     *
     * @param AbstractRenderer $renderer an instance of the rendering object to use for generating the diff
     *
     * @return string the generated diff
     */
    public function render(AbstractRenderer $renderer): string
    {
        $renderer->setDiff($this);

        // the "no difference" situation may happen frequently
        // let's save some calculation if possible
        return $this->a === $this->b
            ? $renderer::getIdenticalResult()
            : $renderer->render();
    }

    /**
     * The work horse of getA() and getB().
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
