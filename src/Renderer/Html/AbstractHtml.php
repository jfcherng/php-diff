<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Differ;
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
     * @var bool is this renderer pure text?
     */
    const IS_TEXT_RENDERER = false;

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
     * {@inheritdoc}
     */
    public function getResultForIdenticalsDefault(): string
    {
        return '';
    }

    /**
     * Render and return an array structure suitable for generating HTML
     * based differences. Generally called by subclasses that generate a
     * HTML based diff and return an array of the changes to show in the diff.
     *
     * @param Differ $differ the differ object
     *
     * @return array an array of the generated changes, suitable for presentation in HTML
     */
    public function getChanges(Differ $differ): array
    {
        $lineRenderer = LineRendererFactory::make(
            $this->options['detailLevel'],
            $differ->getOptions(),
            $this->options
        );

        $old = $differ->getOld();
        $new = $differ->getNew();

        $changes = [];

        foreach ($differ->getGroupedOpcodes() as $opcodes) {
            $blocks = [];
            $lastTag = SequenceMatcher::OP_NOP;
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
                    $blocks[$lastBlock]['old']['lines'] += $this->formatLines($lines);
                    $lines = \array_slice($new, $j1, $j2 - $j1);
                    $blocks[$lastBlock]['new']['lines'] += $this->formatLines($lines);

                    continue;
                }

                if ($tag & (SequenceMatcher::OP_REP | SequenceMatcher::OP_DEL)) {
                    $lines = \array_slice($old, $i1, $i2 - $i1);
                    $lines = $this->formatLines($lines);
                    $lines = \str_replace(
                        RendererConstant::HTML_CLOSURES,
                        RendererConstant::HTML_CLOSURES_DEL,
                        $lines
                    );

                    $blocks[$lastBlock]['old']['lines'] += $lines;
                }

                if ($tag & (SequenceMatcher::OP_REP | SequenceMatcher::OP_INS)) {
                    $lines = \array_slice($new, $j1, $j2 - $j1);
                    $lines = $this->formatLines($lines);
                    $lines = \str_replace(
                        RendererConstant::HTML_CLOSURES,
                        RendererConstant::HTML_CLOSURES_INS,
                        $lines
                    );

                    $blocks[$lastBlock]['new']['lines'] += $lines;
                }
            }

            $changes[] = $blocks;
        }

        return $changes;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderWorker(Differ $differ): string
    {
        return $this->redererChanges($this->getChanges($differ));
    }

    /**
     * {@inheritdoc}
     */
    protected function renderArrayWorker(array $differArray): string
    {
        return $this->redererChanges($this->ensureChangesUseIntTag($differArray));
    }

    /**
     * Render the array of changes.
     *
     * @param array $changes the changes
     */
    abstract protected function redererChanges(array $changes): string;

    /**
     * Renderer the changed extent.
     *
     * @param AbstractLineRenderer $lineRenderer the line renderer
     * @param string               $old          the old line
     * @param string               $new          the new line
     *
     * @throws \InvalidArgumentException
     *
     * @return static
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
     * @param int $tag the operation tag
     * @param int $i1  begin index of the diff of the old array
     * @param int $j1  begin index of the diff of the new array
     *
     * @return array the default block
     */
    protected function getDefaultBlock(int $tag, int $i1, int $j1): array
    {
        return [
            'tag' => $tag,
            'old' => [
                'offset' => $i1,
                'lines' => [],
            ],
            'new' => [
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
                function (array $matches) use ($tabSize): string {
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

    /**
     * Make sure the "changes" array uses int "tag".
     *
     * Internally, we would like always int form for better performance.
     *
     * @param array $changes the changes
     */
    protected function ensureChangesUseIntTag(array $changes): array
    {
        if (empty($changes)) {
            return [];
        }

        $isTagInt = true;
        foreach ($changes as $blocks) {
            foreach ($blocks as $change) {
                $isTagInt = \is_int($change['tag']);

                break 2;
            }
        }

        if (!$isTagInt) {
            // convert string tags into their int forms
            foreach ($changes as &$blocks) {
                foreach ($blocks as &$change) {
                    $change['tag'] = SequenceMatcher::opStrToInt($change['tag']);
                }
            }

            unset($blocks, $change);
        }

        return $changes;
    }
}
