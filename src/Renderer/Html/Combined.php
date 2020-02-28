<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Factory\LineRendererFactory;
use Jfcherng\Diff\Renderer\RendererConstant;
use Jfcherng\Diff\SequenceMatcher;
use Jfcherng\Diff\Utility\ReverseIterator;
use Jfcherng\Utility\MbString;

/**
 * Combined HTML diff generator.
 *
 * Note that this renderer always has no line number.
 */
final class Combined extends AbstractHtml
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Combined',
        'type' => 'Html',
    ];

    /**
     * {@inheritdoc}
     */
    protected function redererChanges(array $changes): string
    {
        if (empty($changes)) {
            return $this->getResultForIdenticals();
        }

        $wrapperClasses = \array_merge(
            $this->options['wrapperClasses'],
            ['diff', 'diff-html', 'diff-combined']
        );

        return
            '<table class="' . \implode(' ', $wrapperClasses) . '">' .
                $this->renderTableHeader() .
                $this->renderTableHunks($changes) .
            '</table>';
    }

    /**
     * Renderer the table header.
     */
    protected function renderTableHeader(): string
    {
        return
            '<thead>' .
                '<tr>' .
                    '<th>' . $this->_('differences') . '</th>' .
                '</tr>' .
            '</thead>';
    }

    /**
     * Renderer the table separate block.
     */
    protected function renderTableSeparateBlock(): string
    {
        return
            '<tbody class="skipped">' .
                '<tr>' .
                    '<td></td>' .
                '</tr>' .
            '</tbody>';
    }

    /**
     * Renderer table hunks.
     *
     * @param array[][] $hunks each hunk has many blocks
     */
    protected function renderTableHunks(array $hunks): string
    {
        $html = '';

        foreach ($hunks as $i => $hunk) {
            if ($i > 0 && $this->options['separateBlock']) {
                $html .= $this->renderTableSeparateBlock();
            }

            foreach ($hunk as $block) {
                $html .= $this->renderTableBlock($block);
            }
        }

        return $html;
    }

    /**
     * Renderer the table block.
     *
     * @param array $block the block
     */
    protected function renderTableBlock(array $block): string
    {
        static $callbacks = [
            SequenceMatcher::OP_EQ => 'renderTableBlockEqual',
            SequenceMatcher::OP_INS => 'renderTableBlockInsert',
            SequenceMatcher::OP_DEL => 'renderTableBlockDelete',
            SequenceMatcher::OP_REP => 'renderTableBlockReplace',
        ];

        return
            '<tbody class="change change-' . self::TAG_CLASS_MAP[$block['tag']] . '">' .
                $this->{$callbacks[$block['tag']]}($block) .
            '</tbody>';
    }

    /**
     * Renderer the table block: equal.
     *
     * @param array $block the block
     */
    protected function renderTableBlockEqual(array $block): string
    {
        $html = '';

        // note that although we are in a OP_EQ situation,
        // the old and the new may not be exactly the same
        // because of ignoreCase, ignoreWhitespace, etc
        foreach ($block['old']['lines'] as $oldLine) {
            // hmm... but there is only space for one line
            // we could only pick either the old or the new to show
            $html .=
                '<tr data-type="=">' .
                    '<td class="old">' . $oldLine . '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: insert.
     *
     * @param array $block the block
     */
    protected function renderTableBlockInsert(array $block): string
    {
        $html = '';

        foreach ($block['new']['lines'] as $newLine) {
            $html .=
                '<tr data-type="+">' .
                    '<td class="new">' . $newLine . '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: delete.
     *
     * @param array $block the block
     */
    protected function renderTableBlockDelete(array $block): string
    {
        $html = '';

        foreach ($block['old']['lines'] as $oldLine) {
            $html .=
                '<tr data-type="-">' .
                    '<td class="old">' . $oldLine . '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: replace.
     *
     * @param array $block the block
     */
    protected function renderTableBlockReplace(array $block): string
    {
        $html = '';

        $oldLines = $block['old']['lines'];
        $newLines = $block['new']['lines'];

        $oldLinesCount = \count($oldLines);
        $newLinesCount = \count($newLines);

        // if the line counts changes, we treat the old and the new as
        // "a line with \n in it" and then do one-line-to-one-line diff
        if ($oldLinesCount !== $newLinesCount) {
            [$oldLines, $newLines] = $this->diffReplaceBlock($oldLines, $newLines);
            $oldLinesCount = $newLinesCount = 1;
        }

        // fix for "detailLevel" is "none"
        $this->fixLinesForNoClosure($oldLines, RendererConstant::HTML_CLOSURES_DEL);
        $this->fixLinesForNoClosure($newLines, RendererConstant::HTML_CLOSURES_INS);

        // now $oldLines must has the same line counts with $newlines
        for ($no = 0; $no < $newLinesCount; ++$no) {
            $html .=
                '<tr data-type="!">' .
                    '<td class="rep">' .
                        $this->mergeReplaceLines($oldLines[$no], $newLines[$no]) .
                    '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Merge two "replace" lines into one line.
     *
     * - Extract newPart in "SOME CONTENT <ins>newPart</ins> SOME CONTENT"
     * - Place "<ins>newPart</ins>" after "<del>oldPart</del>"
     *
     * @param string $oldLine the old line
     * @param string $newLine the new line
     */
    protected function mergeReplaceLines(string $oldLine, string $newLine): string
    {
        $delParts = $this->extractClosureParts(
            RendererConstant::HTML_CLOSURES_DEL[0],
            RendererConstant::HTML_CLOSURES_DEL[1],
            $oldLine,
            SequenceMatcher::OP_DEL
        );

        $insParts = $this->extractClosureParts(
            RendererConstant::HTML_CLOSURES_INS[0],
            RendererConstant::HTML_CLOSURES_INS[1],
            $newLine,
            SequenceMatcher::OP_INS
        );

        // create a sorted merged parts array
        $mergedParts = \array_merge($delParts, $insParts);
        \usort($mergedParts, function (array $a, array $b): int {
            // first sort by "offsetBiased" then by "type"
            return $a['offsetBiased'] <=> $b['offsetBiased']
                ?: ($a['type'] === SequenceMatcher::OP_DEL ? -1 : 1);
        });

        // get the cleaned line by a non-regex way (should be faster)
        // i.e., remove all "<ins>...</ins>" parts from the new line
        $line = '';
        $offset = 0;
        foreach ($insParts as $insPart) {
            $line .= \substr($newLine, $offset, $insPart['offset'] - $offset);
            $offset = $insPart['offset'] + \strlen($insPart['content']);
        }
        $line .= \substr($newLine, $offset);

        // insert merged parts into the cleaned line
        foreach (ReverseIterator::fromArray($mergedParts) as $part) {
            $line = \substr_replace(
                $line,
                $part['content'],
                $part['offsetBiased'],
                0 // insertion
            );
        }

        return $line;
    }

    /**
     * Extract the closure parts of the line.
     *
     * Such as
     *     extract "<ins>part 1</ins>" and "<ins>part 2</ins>"
     *     from "<ins>part 1</ins>SOME OTHER TEXT<ins>part 2</ins>"
     *
     * Note that preg_match_all() is handy but slow.
     *
     * @param string $ld   the left delimiter
     * @param string $rd   the right delimiter
     * @param string $line the line
     * @param int    $type the line type
     *
     * @see https://stackoverflow.com/a/27078384/12866913 (this method)
     * @see https://stackoverflow.com/a/27071699/4643765 (preg_match_all)
     */
    protected function extractClosureParts(string $ld, string $rd, string $line, int $type): array
    {
        $ldLength = \strlen($ld);
        $rdLength = \strlen($rd);

        $parts = [];
        $partStart = $partEnd = 0;
        $offsetBias = 0;

        while (false !== ($partStart = \strpos($line, $ld, $partEnd))) {
            if (false === ($partEnd = \strpos($line, $rd, $partStart + $ldLength))) {
                break;
            }

            $partEnd += $rdLength;
            $partLength = $partEnd - $partStart;

            $parts[] = [
                'type' => $type,
                'offset' => $partStart,
                'offsetBiased' => $partStart - $offsetBias,
                'content' => \substr($line, $partStart, $partLength),
            ];

            $offsetBias += $partLength;
        }

        return $parts;
    }

    /**
     * Mark differences between two "replace" blocks.
     *
     * Each of the returned block (lines) is always only one line.
     *
     * @param string[] $oldBlock The old block
     * @param string[] $newBlock The new block
     *
     * @return string[][] the value of [[$oldLine], [$newLine]]
     */
    protected function diffReplaceBlock(array $oldBlock, array $newBlock): array
    {
        static $isInitiated = false, $mbOld, $mbNew, $lineRenderer;

        if (!$isInitiated) {
            $isInitiated = true;

            $mbOld = new MbString();
            $mbNew = new MbString();
            $lineRenderer = LineRendererFactory::make(
                $this->options['detailLevel'],
                [], /** @todo is it possible to get the differOptions here? */
                $this->options
            );
        }

        $mbOld->set(\implode("\n", $oldBlock));
        $mbNew->set(\implode("\n", $newBlock));

        $lineRenderer->render($mbOld, $mbNew);

        $oldLine = \str_replace(
            RendererConstant::HTML_CLOSURES,
            RendererConstant::HTML_CLOSURES_DEL,
            $mbOld->get()
        );

        $newLine = \str_replace(
            RendererConstant::HTML_CLOSURES,
            RendererConstant::HTML_CLOSURES_INS,
            $mbNew->get()
        );

        return [
            [$oldLine], // one-line block for the old
            [$newLine], // one-line block for the new
        ];
    }

    /**
     * Wrap the whole line with closures if it does not have one.
     *
     * @param string[] $lines    the lines
     * @param string[] $closures the closures
     */
    protected function fixLinesForNoClosure(array &$lines, array $closures): void
    {
        foreach ($lines as &$line) {
            // there is no closure in a "replace"-type line
            // this means that the entire line changes
            if (false === \strpos($line, $closures[0])) {
                $line = "{$closures[0]}{$line}{$closures[1]}";
            }
        }
    }
}
