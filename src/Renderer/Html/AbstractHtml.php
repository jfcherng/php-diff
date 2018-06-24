<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Renderer\AbstractRenderer;
use Jfcherng\Diff\Utility\SequenceMatcher;
use Jfcherng\Utility\LevenshteinDistance;
use Jfcherng\Utility\MbString;
use RuntimeException;

/**
 * Base renderer for rendering HTML based diffs.
 */
abstract class AbstractHtml extends AbstractRenderer
{
    /**
     * @var bool Is this template pure text?
     */
    const IS_HTML_TEMPLATE = true;

    /**
     * @var array array of the different opcode tags and how they map to the HTML class
     */
    const TAG_CLASS_MAP = [
        SequenceMatcher::OPCODE_DELETE => 'Delete',
        SequenceMatcher::OPCODE_EQUAL => 'Equal',
        SequenceMatcher::OPCODE_INSERT => 'Insert',
        SequenceMatcher::OPCODE_REPLACE => 'Replace',
    ];

    protected $titleOld = 'Old';
    protected $titleNew = 'New';
    protected $titleDiff = 'Differences';

    /**
     * closures that are used to enclose.
     *
     * - a different part in string (class internal)
     * - a inserted char in output HTML
     * - a deleted char in output HTML
     *
     * @var string[]
     */
    protected static $closures = ["\0", "\1"];
    protected static $closuresIns = ['<ins>', '</ins>'];
    protected static $closuresDel = ['<del>', '</del>'];

    /**
     * The constructor.
     *
     * @param array $options The options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        foreach (['titleOld', 'titleNew', 'titleDiff'] as $option) {
            if (isset($this->options[$option])) {
                $this->$option = $this->options[$option];
            }
        }
    }

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

        $mbFromLine = new MbString('', 'UTF-8');
        $mbToLine = new MbString('', 'UTF-8');
        $changes = [];
        $opcodes = $this->diff->getGroupedOpcodes();

        foreach ($opcodes as $group) {
            $blocks = [];
            $lastTag = null;
            $lastBlock = 0;

            foreach ($group as $opcode) {
                [$tag, $i1, $i2, $j1, $j2] = $opcode;

                if (
                    $tag === SequenceMatcher::OPCODE_REPLACE &&
                    $i2 - $i1 === $j2 - $j1
                ) {
                    for ($i = 0; $i < $i2 - $i1; ++$i) {
                        // start to render two corresponding lines
                        $fromLine = &$a[$i1 + $i];
                        $toLine = &$b[$j1 + $i];
                        $mbFromLine->set($fromLine);
                        $mbToLine->set($toLine);
                        $this->renderChangedExtent($mbFromLine, $mbToLine);
                        $fromLine = $mbFromLine->get();
                        $toLine = $mbToLine->get();
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
                    $lastBlock = count($blocks) - 1;
                }

                $lastTag = $tag;

                if ($tag === SequenceMatcher::OPCODE_EQUAL) {
                    $lines = array_slice($a, $i1, ($i2 - $i1));
                    $blocks[$lastBlock]['base']['lines'] += $this->formatLines($lines);
                    $lines = array_slice($b, $j1, ($j2 - $j1));
                    $blocks[$lastBlock]['changed']['lines'] += $this->formatLines($lines);
                } else {
                    if (
                        $tag === SequenceMatcher::OPCODE_REPLACE ||
                        $tag === SequenceMatcher::OPCODE_DELETE
                    ) {
                        $lines = array_slice($a, $i1, ($i2 - $i1));
                        $lines = $this->formatLines($lines);
                        $lines = str_replace(static::$closures, static::$closuresDel, $lines);
                        $blocks[$lastBlock]['base']['lines'] += $lines;
                    }
                    if (
                        $tag === SequenceMatcher::OPCODE_REPLACE ||
                        $tag === SequenceMatcher::OPCODE_INSERT
                    ) {
                        $lines = array_slice($b, $j1, ($j2 - $j1));
                        $lines = $this->formatLines($lines);
                        $lines = str_replace(static::$closures, static::$closuresIns, $lines);
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
        if (!static::IS_HTML_TEMPLATE) {
            return $lines;
        }

        // this delimiter contains chars from the Unicode reserved area
        // hopefully, it won't appear in our lines
        static $delimiter = "\u{ff2fa}\u{fcffc}\u{fff42}";

        // glue all lines into a single string to get rid of multiple function calls later
        $string = implode($delimiter, $lines);

        $string = $this->htmlSafe($string);
        $string = $this->expandTabs($string);
        $string = $this->fixSpaces($string);

        // split the string back to lines
        return explode($delimiter, $string);
    }

    /**
     * Renderer the changed extent.
     *
     * @param MbString $mbFromLine the megabytes from line
     * @param MbString $mbToLine   the megabytes to line
     *
     * @return self
     */
    protected function renderChangedExtent(MbString $mbFromLine, MbString $mbToLine): self
    {
        if ($mbFromLine->get(true) === $mbToLine->get(true)) {
            return $this;
        }

        return $this->diff->options['charLevelDiff']
            ? $this->renderChangedExtentCharLevel($mbFromLine, $mbToLine)
            : $this->renderChangedExtentLineLevel($mbFromLine, $mbToLine);
    }

    /**
     * Renderer the changed extent at line level.
     *
     * @param MbString $mbFromLine the megabytes from line
     * @param MbString $mbToLine   the megabytes to line
     *
     * @return self
     */
    protected function renderChangedExtentLineLevel(MbString $mbFromLine, MbString $mbToLine): self
    {
        [$start, $end] = $this->getChangeExtent($mbFromLine, $mbToLine);

        // two strings are the same
        if ($end === 0) {
            return $this;
        }

        // two strings are different, we do rendering
        $mbFromLine->str_enclose_i(
            static::$closures,
            $start,
            $end + $mbFromLine->strlen() - $start + 1
        );
        $mbToLine->str_enclose_i(
            static::$closures,
            $start,
            $end + $mbToLine->strlen() - $start + 1
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
     * @param MbString $mbFromLine the megabytes from line
     * @param MbString $mbToLine   the megabytes to line
     *
     * @return self
     */
    protected function renderChangedExtentCharLevel(MbString $mbFromLine, MbString $mbToLine): self
    {
        $fromLastEditPos = $mbFromLine->strlen();
        $toLastEditPos = $mbToLine->strlen();

        // we prefer the char-level diff but if there is an exception like
        // "line too long", we fallback to line-level diff.
        try {
            $editInfo = LevenshteinDistance::calculate(
                $mbFromLine->get(),
                $mbToLine->get(),
                LevenshteinDistance::PROGRESS_SIMPLE
            );
        } catch (RuntimeException $e) {
            return $this->renderChangedExtentLineLevel($mbFromLine, $mbToLine);
        }

        // start to render
        foreach ($editInfo['progresses'] as $step => $operation) {
            /*
             * Note: The representation of 'del' is a special case.
             *       It means do delete until that number of char.
             *       So I use 'lastEditPos' to handle it.
             */
            switch ($operation) {
                case LevenshteinDistance::OP_COPY: // copy, render nothing
                    --$fromLastEditPos;
                    --$toLastEditPos;
                    break;
                case LevenshteinDistance::OP_DELETE: // delete, render 'from'
                    --$fromLastEditPos;
                    $mbFromLine->str_enclose_i(static::$closures, $fromLastEditPos, 1);
                    break;
                case LevenshteinDistance::OP_INSERT: // insert, render 'to'
                    --$toLastEditPos;
                    $mbToLine->str_enclose_i(static::$closures, $toLastEditPos, 1);
                    break;
                case LevenshteinDistance::OP_REPLACE: // replace, render both
                    --$fromLastEditPos;
                    $mbFromLine->str_enclose_i(static::$closures, $fromLastEditPos, 1);
                    --$toLastEditPos;
                    $mbToLine->str_enclose_i(static::$closures, $toLastEditPos, 1);
                    break;
            }
        }

        // check for lastEditPos, render for the string head
        // Note: at least, one of the lastEditPos must be zero
        assert($fromLastEditPos === 0 || $toLastEditPos === 0);

        if ($fromLastEditPos !== $toLastEditPos) {
            if ($fromLastEditPos === 0) {
                $mbToLine->str_enclose_i(static::$closures, 0, $toLastEditPos);
            } else {
                $mbFromLine->str_enclose_i(static::$closures, 0, $fromLastEditPos);
            }
        }

        // cleanup redundant tags
        $mbFromLine->str_replace_i(static::$closures[1] . static::$closures[0], '');
        $mbToLine->str_replace_i(static::$closures[1] . static::$closures[0], '');

        return $this;
    }

    /**
     * Given two strings, determine where the changes in the two strings begin,
     * and where the changes in the two strings end.
     *
     * @param MbString $mbFromLine the first string
     * @param MbString $mbToLine   the second string
     *
     * @return array Array containing the starting position (non-negative) and the ending position (negative)
     *               array(0, 0) if two strings are the same
     */
    protected function getChangeExtent(MbString $mbFromLine, MbString $mbToLine): array
    {
        // calculate $start
        $start = 0;
        $startLimit = min($mbFromLine->strlen(), $mbToLine->strlen());
        while (
            $start < $startLimit && // index out of range
            $mbFromLine->getAtRaw($start) === $mbToLine->getAtRaw($start)
        ) {
            ++$start;
        }

        // calculate $end
        $end = -1; // trick
        $endLimit = $startLimit - $start;
        while (
            -$end <= $endLimit && // index out of range
            $mbFromLine->getAtRaw($end) === $mbToLine->getAtRaw($end)
        ) {
            --$end;
        }

        // two strings are the same
        if ($start === $startLimit && $end === $endLimit) {
            return [0, 0];
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
        return str_replace("\t", str_repeat(' ', $this->options['tabSize']), $string);
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
        return htmlspecialchars($string, ENT_NOQUOTES, 'UTF-8');
    }

    /**
     * Replace a string containing spaces with a HTML representation using &nbsp;.
     *
     * @param string $string the string of spaces
     *
     * @return string the HTML representation of the string
     */
    protected function fixSpaces(string $string): string
    {
        return preg_replace_callback(
            '# {2,}#S', // only fix for more than 1 space
            function (array $matches): string {
                $count = strlen($matches[0]);

                return ($count & 1 ? '&nbsp;' : '') . str_repeat(' &nbsp;', $count >> 1);
            },
            $string
        );
    }
}
