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
        SequenceMatcher::OP_DEL => 'del',
        SequenceMatcher::OP_EQ => 'eq',
        SequenceMatcher::OP_INS => 'ins',
        SequenceMatcher::OP_REP => 'rep',
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

        // As we'll be modifying old & new to include our change markers,
        // we need to get the contents and store them here. That way
        // we're not going to destroy the original data
        $old = $this->diff->getOld();
        $new = $this->diff->getNew();

        $changes = [];

        foreach ($this->diff->getGroupedOpcodes() as $opcodes) {
            $blocks = [];
            $lastTag = null;
            $lastBlock = 0;

            foreach ($opcodes as [$tag, $i1, $i2, $j1, $j2]) {
                if (
                    $tag === SequenceMatcher::OP_REP &&
                    $i2 - $i1 === $j2 - $j1
                ) {
                    for ($i = 0; $i < $i2 - $i1; ++$i) {
                        $this->renderChangedExtent($lineRenderer, $old[$i1 + $i], $new[$j1 + $i]);
                    }
                }

                if ($tag !== $lastTag) {
                    $blocks[] = $this->getDefaultBlock($tag, $i1, $j1);
                    $lastBlock = \count($blocks) - 1;
                }

                $lastTag = $tag;

                if ($tag === SequenceMatcher::OP_EQ) {
                    // note that although we are in a OP_EQ situation,
                    // the old and the new may not be exactly the same
                    // because of ignoreCase, ignoreWhitespace, etc
                    $lines = \array_slice($old, $i1, $i2 - $i1);
                    $blocks[$lastBlock]['base']['lines'] += $this->formatLines($lines);
                    $lines = \array_slice($new, $j1, $j2 - $j1);
                    $blocks[$lastBlock]['changed']['lines'] += $this->formatLines($lines);

                    continue;
                }

                /**
                 * @todo By setting option "useIntOpcodes" for the sequence matcher,
                 *       this "if" could be further optimized by using bit operations.
                 *
                 *       Like this: "if ($tag & (OP_INT_REP | OP_INT_DEL))"
                 *
                 *       But int tag would be less readable while debugging.
                 *       Also, this would be a BC break for the output of the JSON renderer.
                 *       Is it worth doing?
                 */
                if (
                    $tag === SequenceMatcher::OP_REP ||
                    $tag === SequenceMatcher::OP_DEL
                ) {
                    $lines = \array_slice($old, $i1, $i2 - $i1);
                    $lines = $this->formatLines($lines);
                    $lines = \str_replace(
                        RendererConstant::HTML_CLOSURES,
                        RendererConstant::HTML_CLOSURES_DEL,
                        $lines
                    );

                    $blocks[$lastBlock]['base']['lines'] += $lines;
                }

                if (
                    $tag === SequenceMatcher::OP_REP ||
                    $tag === SequenceMatcher::OP_INS
                ) {
                    $lines = \array_slice($new, $j1, $j2 - $j1);
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
     * @param string               $old          the old line
     * @param string               $new          the new line
     *
     * @throws \InvalidArgumentException
     *
     * @return self
     */
    protected function renderChangedExtent(AbstractLineRenderer $lineRenderer, string &$old, string &$new): self
    {
        static $mbOld, $mbNew;

        $mbOld = $mbOld ?? new MbString();
        $mbNew = $mbNew ?? new MbString();

        $mbOld->set($old);
        $mbNew->set($new);

        $lineRenderer->render($mbOld, $mbNew);

        $old = $mbOld->get();
        $new = $mbNew->get();

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
     * Make a series of lines suitable for outputting in a HTML rendered diff.
     *
     * @param string[] $lines array of lines to format
     *
     * @return string[] array of the formatted lines
     */
    protected function formatLines(array $lines): array
    {
        /**
         * To prevent from invoking the same function calls for several times,
         * we can glue lines into a string and call functions for one time.
         * After that, we split the string back into lines.
         */
        return \explode(
            RendererConstant::IMPLODE_DELIMITER,
            $this->formatStringFromLines(\implode(
                RendererConstant::IMPLODE_DELIMITER,
                $lines
            ))
        );
    }

    /**
     * Make a string suitable for outputting in a HTML rendered diff.
     *
     * This my involve replacing tab characters with spaces, making the HTML safe
     * for output, ensuring that double spaces are replaced with &nbsp; etc.
     *
     * @param string $string the string of imploded lines
     *
     * @return string the formatted string
     */
    protected function formatStringFromLines(string $string): string
    {
        $string = $this->expandTabs($string, $this->options['tabSize']);
        $string = $this->htmlSafe($string);

        if ($this->options['spacesToNbsp']) {
            $string = $this->htmlFixSpaces($string);
        }

        return $string;
    }

    /**
     * Replace tabs in a string with a number of spaces.
     *
     * @param string $string          the containing tabs to convert
     * @param int    $tabSize         one tab = how many spaces, a negative does nothing
     * @param bool   $onlyLeadingTabs only expand leading tabs
     *
     * @return string the string with the tabs converted to spaces
     */
    protected function expandTabs(string $string, int $tabSize = 4, bool $onlyLeadingTabs = false): string
    {
        if ($tabSize < 0) {
            return $string;
        }

        if ($onlyLeadingTabs) {
            return \preg_replace_callback(
                "/^[ \t]{1,}/mS", // tabs and spaces may be mixed
                function (array $matches): string {
                    return \str_replace("\t", \str_repeat(' ', $tabSize), $matches[0]);
                },
                $string
            );
        }

        return \str_replace("\t", \str_repeat(' ', $tabSize), $string);
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
