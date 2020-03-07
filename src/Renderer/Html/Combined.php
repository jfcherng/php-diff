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
        foreach ($block['new']['lines'] as $newLine) {
            // but in this renderer, we can only pick either the old or the new to show
            $html .=
                '<tr data-type="=">' .
                    '<td class="new">' . $newLine . '</td>' .
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
            [$oldLines, $newLines] = $this->markReplaceBlockDiff($oldLines, $newLines);
            $oldLinesCount = $newLinesCount = 1;
        }

        // fix for "detailLevel" is "none"
        if ($this->options['detailLevel'] === 'none') {
            $this->fixLinesForNoClosure($oldLines, RendererConstant::HTML_CLOSURES_DEL);
            $this->fixLinesForNoClosure($newLines, RendererConstant::HTML_CLOSURES_INS);
        }

        // now $oldLines must has the same line counts with $newlines
        for ($no = 0; $no < $newLinesCount; ++$no) {
            $mergedLine = $this->mergeReplaceLines($oldLines[$no], $newLines[$no]);

            if (!isset($mergedLine)) {
                $html .= $this->renderTableBlockDelete($block) . $this->renderTableBlockInsert($block);

                continue;
            }

            $html .=
                '<tr data-type="!">' .
                    '<td class="rep">' .
                        $mergedLine .
                    '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Merge two "replace"-type lines into a single line.
     *
     * The implementation concept is that if we remove all closure parts from
     * the old and the new, the rest of them (cleaned line) should be the same.
     * And then, we add back those removed closure parts in a correct order.
     *
     * @param string $oldLine the old line
     * @param string $newLine the new line
     *
     * @return null|string string if merge-able, null otherwise
     */
    protected function mergeReplaceLines(string $oldLine, string $newLine): ?string
    {
        $delParts = $this->analyzeClosureParts(
            RendererConstant::HTML_CLOSURES_DEL[0],
            RendererConstant::HTML_CLOSURES_DEL[1],
            $oldLine,
            SequenceMatcher::OP_DEL
        );

        $insParts = $this->analyzeClosureParts(
            RendererConstant::HTML_CLOSURES_INS[0],
            RendererConstant::HTML_CLOSURES_INS[1],
            $newLine,
            SequenceMatcher::OP_INS
        );

        // get the cleaned line by a non-regex way (should be faster)
        // i.e., the new line with all "<ins>...</ins>" parts removed
        $mergedLine = $newLine;
        foreach (ReverseIterator::fromArray($insParts) as $part) {
            $mergedLine = \substr_replace(
                $mergedLine,
                '', // deletion
                $part['offset'],
                $part['length']
            );
        }

        // note that $mergedLine is actually a clean line at this point
        if (!$this->isLinesMergeable($oldLine, $newLine, $mergedLine)) {
            return null;
        }

        // before building the $mergedParts, we do some adjustments
        $this->revisePartsForBoundaryNewlines($delParts, RendererConstant::HTML_CLOSURES_DEL);
        $this->revisePartsForBoundaryNewlines($insParts, RendererConstant::HTML_CLOSURES_INS);

        // create a sorted merged parts array
        $mergedParts = \array_merge($delParts, $insParts);
        \usort($mergedParts, function (array $a, array $b): int {
            // first sort by "offsetClean", "order" then by "type"
            return $a['offsetClean'] <=> $b['offsetClean']
                ?: $a['order'] <=> $b['order']
                ?: ($a['type'] === SequenceMatcher::OP_DEL ? -1 : 1);
        });

        // insert merged parts into the cleaned line
        foreach (ReverseIterator::fromArray($mergedParts) as $part) {
            $mergedLine = \substr_replace(
                $mergedLine,
                $part['content'],
                $part['offsetClean'],
                0 // insertion
            );
        }

        return \str_replace("\n", '<br>', $mergedLine);
    }

    /**
     * Analyze and get the closure parts information of the line.
     *
     * Such as
     *     extract informations for "<ins>part 1</ins>" and "<ins>part 2</ins>"
     *     from "Hello <ins>part 1</ins>SOME OTHER TEXT<ins>part 2</ins> World"
     *
     * @param string $ld   the left delimiter
     * @param string $rd   the right delimiter
     * @param string $line the line
     * @param int    $type the line type
     *
     * @return array[] the closure informations
     */
    protected function analyzeClosureParts(string $ld, string $rd, string $line, int $type): array
    {
        $ldLength = \strlen($ld);
        $rdLength = \strlen($rd);

        $parts = [];
        $partStart = $partEnd = 0;
        $partLengthSum = 0;

        // find the next left delimiter
        while (false !== ($partStart = \strpos($line, $ld, $partEnd))) {
            // find the corresponding right delimiter
            if (false === ($partEnd = \strpos($line, $rd, $partStart + $ldLength))) {
                break;
            }

            $partEnd += $rdLength;
            $partLength = $partEnd - $partStart;

            $parts[] = [
                'type' => $type,
                // the sorting order used when both "offsetClean" are the same
                'order' => 0,
                // the offset in the line
                'offset' => $partStart,
                'length' => $partLength,
                // the offset in the cleaned line (i.e., the line with closure parts removed)
                'offsetClean' => $partStart - $partLengthSum,
                // the content of the part
                'content' => \substr($line, $partStart, $partLength),
            ];

            $partLengthSum += $partLength;
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
    protected function markReplaceBlockDiff(array $oldBlock, array $newBlock): array
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

    /**
     * Determine whether the "replace"-type lines are merge-able or not.
     *
     * @param string $oldLine   the old line
     * @param string $newLine   the new line
     * @param string $cleanLine the clean line
     */
    protected function isLinesMergeable(string $oldLine, string $newLine, string $cleanLine): bool
    {
        $oldLine = \str_replace(RendererConstant::HTML_CLOSURES_DEL, '', $oldLine);
        $newLine = \str_replace(RendererConstant::HTML_CLOSURES_INS, '', $newLine);

        $sumLength = \strlen($oldLine) + \strlen($newLine);

        /** @var float the changed ratio, 0 <= range < 1 */
        $changedRatio = ($sumLength - (\strlen($cleanLine) << 1)) / ($sumLength + 1);

        return $changedRatio < 0.8;
    }

    /**
     * Extract boundary newlines from parts into new parts.
     *
     * @param array[]  $parts    the parts
     * @param string[] $closures the closures
     *
     * @see https://git.io/JvVXH
     */
    protected function revisePartsForBoundaryNewlines(array &$parts, array $closures): void
    {
        $closureRegexL = \preg_quote($closures[0], '/');
        $closureRegexR = \preg_quote($closures[1], '/');

        for ($i = \count($parts) - 1; $i >= 0; --$i) {
            $part = &$parts[$i];

            // deal with leading newlines
            $part['content'] = \preg_replace_callback(
                "/(?P<closure>{$closureRegexL})(?P<nl>[\r\n]++)/u",
                function (array $matches) use (&$parts, $part, $closures): string {
                    // add a new part for the extracted newlines
                    $part['order'] = -1;
                    $part['content'] = "{$closures[0]}{$matches['nl']}{$closures[1]}";
                    $parts[] = $part;

                    return $matches['closure'];
                },
                $part['content']
            );

            // deal with trailing newlines
            $part['content'] = \preg_replace_callback(
                "/(?P<nl>[\r\n]++)(?P<closure>{$closureRegexR})/u",
                function (array $matches) use (&$parts, $part, $closures): string {
                    // add a new part for the extracted newlines
                    $part['order'] = 1;
                    $part['content'] = "{$closures[0]}{$matches['nl']}{$closures[1]}";
                    $parts[] = $part;

                    return $matches['closure'];
                },
                $part['content']
            );
        }
    }
}
