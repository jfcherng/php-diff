<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Factory\LineRendererFactory;
use Jfcherng\Diff\Renderer\AbstractRenderer;
use Jfcherng\Diff\Renderer\Html\LineRenderer\AbstractLineRenderer;
use Jfcherng\Diff\Renderer\RendererConstant;
use Jfcherng\Diff\SequenceMatcher;
use Jfcherng\Utility\MbString;

/**
 * Base renderer for rendering HTML-based diffs.
 */
abstract class AbstractHtml extends AbstractRenderer
{
    /**
     * @var bool is this template pure text?
     */
    const IS_TEXT_TEMPLATE = false;

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
        $lineRenderer = LineRendererFactory::make(
            $this->options['detailLevel'],
            $this->diff->getOptions(),
            $this->options
        );

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
                        $this->renderChangedExtent($lineRenderer, $a[$i1 + $i], $b[$j1 + $i]);
                    }
                }

                if ($tag !== $lastTag) {
                    $blocks[] = $this->getDefaultBlock($tag, $i1, $j1);
                    $lastBlock = \count($blocks) - 1;
                }

                $lastTag = $tag;

                if ($tag === SequenceMatcher::OPCODE_EQUAL) {
                    if (!empty($lines = \array_slice($a, $i1, ($i2 - $i1)))) {
                        $formattedLines = $this->formatLines($lines);

                        $blocks[$lastBlock]['base']['lines'] += $formattedLines;
                        $blocks[$lastBlock]['changed']['lines'] += $formattedLines;
                    }

                    continue;
                }

                if (
                    $tag === SequenceMatcher::OPCODE_REPLACE ||
                    $tag === SequenceMatcher::OPCODE_DELETE
                ) {
                    $lines = \array_slice($a, $i1, ($i2 - $i1));
                    $lines = $this->formatLines($lines);
                    $lines = \str_replace(
                        RendererConstant::HTML_CLOSURES,
                        RendererConstant::HTML_CLOSURES_DEL,
                        $lines
                    );
                    $blocks[$lastBlock]['base']['lines'] += $lines;
                }

                if (
                    $tag === SequenceMatcher::OPCODE_REPLACE ||
                    $tag === SequenceMatcher::OPCODE_INSERT
                ) {
                    $lines = \array_slice($b, $j1, ($j2 - $j1));
                    $lines = $this->formatLines($lines);
                    $lines = \str_replace(
                        RendererConstant::HTML_CLOSURES,
                        RendererConstant::HTML_CLOSURES_INS,
                        $lines
                    );
                    $blocks[$lastBlock]['changed']['lines'] += $lines;
                }
            }

            $changes[] = $blocks;
        }

        return $changes;
    }

    /**
     * Renderer the changed extent.
     *
     * @param AbstractLineRenderer $lineRenderer the line renderer
     * @param string               $from         the from line
     * @param string               $to           the to line
     *
     * @throws \InvalidArgumentException
     *
     * @return self
     */
    protected function renderChangedExtent(AbstractLineRenderer $lineRenderer, string &$from, string &$to): self
    {
        static $mbFrom, $mbTo;

        $mbFrom = $mbFrom ?? new MbString();
        $mbTo = $mbTo ?? new MbString();

        $mbFrom->set($from);
        $mbTo->set($to);

        $lineRenderer->render($mbFrom, $mbTo);

        $from = $mbFrom->get();
        $to = $mbTo->get();

        return $this;
    }

    /**
     * Get the default block.
     *
     * @param string $tag the operation tag
     * @param int    $i1  begin index of the diff of the source
     * @param int    $j1  begin index of the diff of the destination
     *
     * @return array the default block
     */
    protected function getDefaultBlock(string $tag, int $i1, int $j1): array
    {
        return [
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
        $string = \implode(RendererConstant::IMPLODE_DELIMITER, $lines);

        $string = $this->expandTabs($string, $this->options['tabSize']);
        $string = $this->htmlSafe($string);

        if ($this->options['spacesToNbsp']) {
            $string = $this->htmlFixSpaces($string);
        }

        // split the string back to lines
        return \explode(RendererConstant::IMPLODE_DELIMITER, $string);
    }

    /**
     * Replace tabs in a string with a number of spaces.
     *
     * @param string $string  the containing tabs to convert
     * @param int    $tabSize one tab = how many spaces, a negative does nothing
     *
     * @return string the string with the tabs converted to spaces
     */
    protected function expandTabs(string $string, int $tabSize = 4): string
    {
        return $tabSize >= 0
            ? \str_replace("\t", \str_repeat(' ', $tabSize), $string)
            : $string;
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
            '/ {2,}/S', // only fix for more than 1 space
            function (array $matches): string {
                $count = \strlen($matches[0]);

                return \str_repeat(' &nbsp;', $count >> 1) . ($count & 1 ? ' ' : '');
            },
            $string
        );
    }
}
