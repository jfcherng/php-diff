<?php

declare(strict_types=1);

namespace Jfcherng\Diff;

use Jfcherng\Diff\Renderer\AbstractRenderer;
use Jfcherng\Diff\Utility\SequenceMatcher;

/**
 * A comprehensive library for generating differences between two strings
 * in multiple formats (unified, side by side HTML etc).
 *
 * @author Jack Cherng <jfcherng@gmail.com>
 * @author Chris Boulton <chris.boulton@interspire.com>
 *
 * @see http://github.com/chrisboulton/php-diff
 */
class Diff
{
    /**
     * @var array array of the options that have been applied for generating the diff
     */
    public $options = [];

    /**
     * @var string[] the "old" sequence to use as the basis for the comparison
     */
    protected $a = [];

    /**
     * @var string[] the "new" sequence to generate the changes for
     */
    protected $b = [];

    /**
     * @var null|array array containing the generated opcodes for the differences between the two items
     */
    protected $groupedCodes;

    /**
     * @var array associative array of the default options available for the diff class and their default value
     */
    protected static $defaultOptions = [
        // enable character-level diff
        'context' => 3,
        // show how many neighbor lines
        'charLevelDiff' => false,
        // ignore case difference
        'ignoreWhitespace' => false,
        // ignore whitespace difference
        'ignoreCase' => false,
        // show "..." row in HTML templates
        'separateBlock' => true,
    ];

    /**
     * The constructor.
     *
     * @param string[] $a       array containing the lines of the first string to compare
     * @param string[] $b       array containing the lines for the second string to compare
     * @param array    $options
     */
    public function __construct(array $a = [], array $b = [], array $options = [])
    {
        $this
            ->setA($a)
            ->setB($b)
            ->setOptions($options);
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

        return $this;
    }

    /**
     * Get a range of lines from $start to $end from the second comparison string
     * and return them as an array. If no values are supplied, the entire string
     * is returned. It's also possible to specify just one line to return only
     * that line.
     *
     * @param int      $start the starting number
     * @param null|int $end   the ending number. If not supplied, only the item in $start will be returned.
     *
     * @return string[] array of all of the lines between the specified range
     */
    public function getA(int $start = 0, ?int $end = null): array
    {
        return $this->getText($this->a, $start, $end);
    }

    /**
     * Get a range of lines from $start to $end from the second comparison string
     * and return them as an array. If no values are supplied, the entire string
     * is returned. It's also possible to specify just one line to return only
     * that line.
     *
     * @param int      $start the starting number
     * @param null|int $end   the ending number. If not supplied, only the item in $start will be returned.
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

        return $singleton ?? new static();
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
        return $this->groupedCodes ??
            (new SequenceMatcher($this->a, $this->b, null, $this->options))
                ->getGroupedOpcodes($this->options['context']);
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
        return $this->a !== $this->b
            ? $renderer->render()
            : $renderer::IDENTICAL_RESULT;
    }

    /**
     * The work horse of getA() and getB().
     *
     * @param string[] $lines the array of lines
     * @param int      $start the starting number
     * @param null|int $end   the ending number. If not supplied, only the item in $start will be returned.
     *
     * @return string[] array of all of the lines between the specified range
     */
    protected function getText(array $lines, int $start = 0, ?int $end = null): array
    {
        if ($start === 0 && (!isset($end) || $end === \count($lines))) {
            return $lines;
        }

        return \array_slice($lines, $start, isset($end) ? $end - $start : 1);
    }
}
