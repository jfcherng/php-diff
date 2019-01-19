<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Renderer\AbstractRenderer;
use Jfcherng\Diff\Utility\SequenceMatcher;
use Jfcherng\Utility\LevenshteinDistance as LD;
use Jfcherng\Utility\MbString;
use RuntimeException;

/**
 * Base renderer for rendering HTML based diffs.
 */
abstract class AbstractHtml extends AbstractRenderer
{
    /**
     * @var bool is this template pure text?
     */
    const IS_TEXT_TEMPLATE = false;

    /**
     * Closures that are used to enclose partial strings.
     *
     * - a different part in string (class internal)
     * - a inserted char in output HTML
     * - a deleted char in output HTML
     *
     * @var string[]
     */
    const CLOSURES = ["\u{fcffc}\u{ff2fb}", "\u{fff41}\u{fcffc}"];
    const CLOSURES_INS = ['<ins>', '</ins>'];
    const CLOSURES_DEL = ['<del>', '</del>'];

    /**
     * The delimiter to be used as the glue in string/array functions.
     *
     * this delimiter contains chars from the Unicode reserved areas
     * hopefully, it won't appear in our lines
     *
     * @var string
     */
    const DELIMITER = "\u{ff2fa}\u{fcffc}\u{fff42}";

    /**
     * @var array array of the different opcode tags and how they map to the HTML class
     */
    const TAG_CLASS_MAP = [
        SequenceMatcher::OPCODE_DELETE => 'del',
        SequenceMatcher::OPCODE_EQUAL => 'eq',
        SequenceMatcher::OPCODE_INSERT => 'ins',
        SequenceMatcher::OPCODE_REPLACE => 'rep',
    ];

    /**
     * Render and return an array structure suitable for generating HTML
     * based differences. Generally called by subclasses that generate a
     * HTML based diff and return an array of the changes to show in the diff.
     *
     * @return array an array of the generated changes, suitable for presentation in HTML
     */
    public function getChanges(): array
    {
        // As we'll be modifying a & b to include our change markers,
        // we need to get the contents and store them here. That way
        // we're not going to destroy the original data
        $a = $this->diff->getA();
        $b = $this->diff->getB();

        $changes = [];

        foreach ($this->diff->getGroupedOpcodes() as $opcodes) {
            $blocks = [];
            $lastTag = null;
            $lastBlock = 0;

            foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
                if (
                    $tag === SequenceMatcher::OPCODE_REPLACE &&
                    $i2 - $i1 === $j2 - $j1
                ) {
                    for ($i = 0; $i < $i2 - $i1; ++$i) {
                        $this->renderChangedExtent($a[$i1 + $i], $b[$j1 + $i]);
                    }
                }

                if ($tag !== $lastTag) {
                    $blocks[] = [
                        'tag' => $tag,
                        'base' => [
                            'offset' => $i1,
                            'lines' => [],
                        ],
                        'changed' => [
                            'offset' => $j1,
                            'lines' => [],
                        ],
                    ];

                    $lastBlock = \count($blocks) - 1;
                }

                $lastTag = $tag;

                if ($tag === SequenceMatcher::OPCODE_EQUAL) {
                    $lines = \array_slice($a, $i1, ($i2 - $i1));
                    $blocks[$lastBlock]['base']['lines'] += $this->formatLines($lines);
                    $lines = \array_slice($b, $j1, ($j2 - $j1));
                    $blocks[$lastBlock]['changed']['lines'] += $this->formatLines($lines);
                } else {
                    if (
                        $tag === SequenceMatcher::OPCODE_REPLACE ||
                        $tag === SequenceMatcher::OPCODE_DELETE
                    ) {
                        $lines = \array_slice($a, $i1, ($i2 - $i1));
                        $lines = $this->formatLines($lines);
                        $lines = \str_replace(self::CLOSURES, self::CLOSURES_DEL, $lines);
                        $blocks[$lastBlock]['base']['lines'] += $lines;
                    }

                    if (
                        $tag === SequenceMatcher::OPCODE_REPLACE ||
                        $tag === SequenceMatcher::OPCODE_INSERT
                    ) {
                        $lines = \array_slice($b, $j1, ($j2 - $j1));
                        $lines = $this->formatLines($lines);
                        $lines = \str_replace(self::CLOSURES, self::CLOSURES_INS, $lines);
                        $blocks[$lastBlock]['changed']['lines'] += $lines;
                    }
                }
            }

            $changes[] = $blocks;
        }

        return $changes;
    }

    /**
     * Format a series of lines suitable for output in a HTML rendered diff.
     * This involves replacing tab characters with spaces, making the HTML safe
     * for output, ensuring that double spaces are replaced with &nbsp; etc.
     *
     * @param string[] $lines array of lines to format
     *
     * @return string[] array of the formatted lines
     */
    protected function formatLines(array $lines): array
    {
        // for example, the "Json" template does not need these
        if (static::IS_TEXT_TEMPLATE) {
            return $lines;
        }

        // glue all lines into a single string to get rid of multiple function calls later
        // unnecessary, but should improve performance if there are many lines
        $string = \implode(self::DELIMITER, $lines);

        $string = $this->expandTabs($string);
        $string = $this->htmlSafe($string);

        if ($this->options['spacesToNbsp']) {
            $string = $this->htmlFixSpaces($string);
        }

        // split the string back to lines
        return \explode(self::DELIMITER, $string);
    }

    /**
     * Renderer the changed extent.
     *
     * @param string &$from the from line
     * @param string &$to   the to line
     *
     * @return self
     */
    protected function renderChangedExtent(string &$from, string &$to): self
    {
        static $mbLines;

        if ($from === $to) {
            return $this;
        }

        $mbLines = $mbLines ?? [
            'from' => new MbString('', 'UTF-8'),
            'to' => new MbString('', 'UTF-8'),
        ];

        $mbLines['from']->set($from);
        $mbLines['to']->set($to);

        $this->diff->options['charLevelDiff']
            ? $this->renderChangedExtentCharLevel($mbLines['from'], $mbLines['to'])
            : $this->renderChangedExtentLineLevel($mbLines['from'], $mbLines['to']);

        $from = $mbLines['from']->get();
        $to = $mbLines['to']->get();

        return $this;
    }

    /**
     * Renderer the changed extent at line level.
     *
     * @param MbString $mbFrom the megabytes from line
     * @param MbString $mbTo   the megabytes to line
     *
     * @return self
     */
    protected function renderChangedExtentLineLevel(MbString $mbFrom, MbString $mbTo): self
    {
        [$start, $end] = $this->getChangeExtent($mbFrom, $mbTo);

        // two strings are the same
        if ($end === 0) {
            return $this;
        }

        // two strings are different, we do rendering
        $mbFrom->str_enclose_i(
            self::CLOSURES,
            $start,
            $end + $mbFrom->strlen() - $start + 1
        );
        $mbTo->str_enclose_i(
            self::CLOSURES,
            $start,
            $end + $mbTo->strlen() - $start + 1
        );

        return $this;
    }

    /**
     * Renderer the changed extent at char level.
     *
     * @todo This method looks like could be rewritten with
     *       Jfcherng\Diff\Utility\SequenceMatcher::getOpcodes and
     *       Jfcherng\Utility\MbString::toArrayRaw.
     *       Don't know how's the performance improvement though.
     *
     * @param MbString $mbFrom the megabytes from line
     * @param MbString $mbTo   the megabytes to line
     *
     * @return self
     */
    protected function renderChangedExtentCharLevel(MbString $mbFrom, MbString $mbTo): self
    {
        // we prefer the char-level diff but if there is an exception like
        // "line too long", we fallback to line-level diff.
        try {
            $editInfo = LD::staticCalculate(
                $mbFrom->get(),
                $mbTo->get(),
                true,
                LD::PROGRESS_MERGE_NEIGHBOR | LD::PROGRESS_NO_COPY
            );
        } catch (RuntimeException $e) {
            return $this->renderChangedExtentLineLevel($mbFrom, $mbTo);
        }

        // start to render
        foreach ($editInfo['progresses'] as [$operation, $fromPos, $toPos, $length]) {
            switch ($operation) {
                // default never happens though
                default:
                // delete, render 'from'
                case LD::OP_DELETE:
                    $mbFrom->str_enclose_i(self::CLOSURES, $fromPos, $length);
                    break;
                // insert, render 'to'
                case LD::OP_INSERT:
                    $mbTo->str_enclose_i(self::CLOSURES, $toPos, $length);
                    break;
                // replace, render both
                case LD::OP_REPLACE:
                    $mbFrom->str_enclose_i(self::CLOSURES, $fromPos, $length);
                    $mbTo->str_enclose_i(self::CLOSURES, $toPos, $length);
                    break;
            }
        }

        // cleanup redundant tags
        $mbFrom->str_replace_i(self::CLOSURES[1] . self::CLOSURES[0], '');
        $mbTo->str_replace_i(self::CLOSURES[1] . self::CLOSURES[0], '');

        return $this;
    }

    /**
     * Given two strings, determine where the changes in the two strings begin,
     * and where the changes in the two strings end.
     *
     * @param MbString $mbFrom the megabytes from line
     * @param MbString $mbTo   the megabytes to line
     *
     * @return array Array containing the starting position (non-negative) and the ending position (negative)
     *               [0, 0] if two strings are the same
     */
    protected function getChangeExtent(MbString $mbFrom, MbString $mbTo): array
    {
        // two strings are the same
        // most lines should be this cases, an early return could save many function calls
        if ($mbFrom->getRaw() === $mbTo->getRaw()) {
            return [0, 0];
        }

        // calculate $start
        $start = 0;
        $startLimit = \min($mbFrom->strlen(), $mbTo->strlen());
        while (
            $start < $startLimit && // index out of range
            $mbFrom->getAtRaw($start) === $mbTo->getAtRaw($start)
        ) {
            ++$start;
        }

        // calculate $end
        $end = -1; // trick
        $endLimit = $startLimit - $start;
        while (
            -$end <= $endLimit && // index out of range
            $mbFrom->getAtRaw($end) === $mbTo->getAtRaw($end)
        ) {
            --$end;
        }

        return [$start, $end];
    }

    /**
     * Replace tabs in a string with a number of spaces as defined by the tabSize option.
     *
     * @param string $string the containing tabs to convert
     *
     * @return string the string with the tabs converted to spaces
     */
    protected function expandTabs(string $string): string
    {
        return \str_replace("\t", \str_repeat(' ', $this->options['tabSize']), $string);
    }

    /**
     * Make a string containing HTML safe for output on a page.
     *
     * @param string $string the string
     *
     * @return string the string with the HTML characters replaced by entities
     */
    protected function htmlSafe(string $string): string
    {
        return \htmlspecialchars($string, \ENT_NOQUOTES, 'UTF-8');
    }

    /**
     * Replace a string containing spaces with a HTML representation having "&nbsp;".
     *
     * @param string $string the string of spaces
     *
     * @return string the HTML representation of the string
     */
    protected function htmlFixSpaces(string $string): string
    {
        return \preg_replace_callback(
            '# {2,}#S', // only fix for more than 1 space
            function (array $matches): string {
                $count = \strlen($matches[0]);

                return \str_repeat(' &nbsp;', $count >> 1) . ($count & 1 ? ' ' : '');
            },
            $string
        );
    }
}
