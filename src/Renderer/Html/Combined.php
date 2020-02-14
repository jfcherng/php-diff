<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\SequenceMatcher;
use Jfcherng\Diff\Renderer\RendererConstant;

/**
 * Combined HTML diff generator
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

        $html = '<table class="' . \implode(' ', $wrapperClasses) . '">';

        $html .= $this->renderTableHeader();

        foreach ($changes as $i => $blocks) {
            if ($i > 0 && $this->options['separateBlock']) {
                $html .= $this->renderTableSeparateBlock();
            }

            foreach ($blocks as $change) {
                $html .= $this->renderTableBlock($change);
            }
        }

        return $html . '</table>';
    }

    /**
     * Renderer the table header.
     */
    protected function renderTableHeader(): string
    {
        return
            '<thead>' .
                '<tr>' .
                    (
                        $this->options['lineNumbers']
                        ?
                            '<th>' . $this->_('old_version') . '</th>' .
                            '<th>' . $this->_('new_version') . '</th>'
                        :
                            ''
                    ) .
                    '<th>' . $this->_('differences') . '</th>' .
                '</tr>' .
            '</thead>';
    }

    /**
     * Renderer the table separate block.
     */
    protected function renderTableSeparateBlock(): string
    {
        $colspan = $this->options['lineNumbers'] ? '3' : '';

        return
            '<tbody class="skipped">' .
                '<tr>' .
                    '<td' . $colspan . '></td>' .
                '</tr>' .
            '</tbody>';
    }

    /**
     * Renderer the table block.
     *
     * @param array $change the change
     */
    protected function renderTableBlock(array $change): string
    {
        static $callbacks = [
            SequenceMatcher::OP_EQ => 'renderTableEqual',
            SequenceMatcher::OP_INS => 'renderTableInsert',
            SequenceMatcher::OP_DEL => 'renderTableDelete',
            SequenceMatcher::OP_REP => 'renderTableReplace',
        ];

        return
            '<tbody class="change change-' . self::TAG_CLASS_MAP[$change['tag']] . '">' .
                $this->{$callbacks[$change['tag']]}($change) .
            '</tbody>';
    }

    /**
     * Renderer the table block: equal.
     *
     * @param array $change the change
     */
    protected function renderTableEqual(array $change): string
    {
        $html = '';

        // note that although we are in a OP_EQ situation,
        // the old and the new may not be exactly the same
        // because of ignoreCase, ignoreWhitespace, etc
        foreach ($change['old']['lines'] as $no => $oldLine) {
            // hmm... but this is a inline renderer
            // we could only pick a line from the old or the new to show
            $oldLineNum = $change['old']['offset'] + $no + 1;
            $newLineNum = $change['new']['offset'] + $no + 1;

            $html .=
                '<tr data-type="=">' .
                    $this->renderLineNumberColumns($oldLineNum, $newLineNum) .
                    '<td class="old">' . $oldLine . '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: insert.
     *
     * @param array $change the change
     */
    protected function renderTableInsert(array $change): string
    {
        $html = '';

        foreach ($change['new']['lines'] as $no => $newLine) {
            $newLineNum = $change['new']['offset'] + $no + 1;

            $html .=
                '<tr data-type="+">' .
                    $this->renderLineNumberColumns(null, $newLineNum) .
                    '<td class="new">' . $newLine . '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: delete.
     *
     * @param array $change the change
     */
    protected function renderTableDelete(array $change): string
    {
        $html = '';

        foreach ($change['old']['lines'] as $no => $oldLine) {
            $oldLineNum = $change['old']['offset'] + $no + 1;

            $html .=
                '<tr data-type="-">' .
                    $this->renderLineNumberColumns($oldLineNum, null) .
                    '<td class="old">' . $oldLine . '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: replace.
     *
     * @param array $change the change
     */
    protected function renderTableReplace(array $change): string
    {
        $html = '';

        $lineCountMax = \max(\count($change['old']['lines']), \count($change['new']['lines']));

        for ($no = 0; $no < $lineCountMax; ++$no) {
            if (isset($change['old']['lines'][$no])) {
                $oldLineNum = $change['old']['offset'] + $no + 1;
                $oldLine = $change['old']['lines'][$no];
            } else {
                $oldLineNum = null;
                $oldLine = '';
            }

            if (isset($change['new']['lines'][$no])) {
                $newLineNum = $change['new']['offset'] + $no + 1;
                $newLine = $change['new']['lines'][$no];
            } else {
                $newLineNum = null;
                $newLine = '';
            }

            $mergeDiffs = $this->mergeDiffs($newLine, $oldLine);

            if ($mergeDiffs != '') {
                $html .=
                '<tr>' .
                  $this->renderLineNumberColumns($oldLineNum, $newLineNum) .
                  '<td class="rep">' . $mergeDiffs . '</td>' .
                '</tr>';
            } else {
                $html .=
                (isset($oldLineNum)
                  ?
                    '<tr>' .
                      $this->renderLineNumberColumns($oldLineNum, null) .
                      '<td class="old">' . $oldLine . '</td>' .
                    '</tr>'
                  : ''
                ) .
                (isset($newLineNum)
                  ?
                    '<tr>' .
                      $this->renderLineNumberColumns(null, $newLineNum) .
                      '<td class="new">' . $newLine . '</td>' .
                    '</tr>'
                  : ''
                );
            }
        }

        return $html;


        $html = '';
    }

    /**
     * Renderer the line number columns.
     *
     * @param null|int $oldLineNum The old line number
     * @param null|int $newLineNum The new line number
     */
    protected function renderLineNumberColumns(?int $oldLineNum, ?int $newLineNum): string
    {
        if (!$this->options['lineNumbers']) {
            return '';
        }

        return
            (
                isset($oldLineNum)
                    ? '<th class="n-old">' . $oldLineNum . '</th>'
                    : '<th></th>'
            ) .
            (
                isset($newLineNum)
                    ? '<th class="n-new">' . $newLineNum . '</th>'
                    : '<th></th>'
            );
    }

    /**
     * Merge diffs between lines.
     *
     * Gets newPart in <ins>newPart</ins>
     * Replaces <del>oldPart</del> with
     * <del>oldPart</del><ins>newPart</ins>
     *
     * @param string $newLine New line
     * @param string $oldLine Old line
     */
    protected function mergeDiffs(string $newLine, string $oldLine): string
    {
        $newParts = $this->getPartsByClosures(
            RendererConstant::HTML_CLOSURES_INS[0],
            RendererConstant::HTML_CLOSURES_INS[1],
            $newLine
        );

        $oldParts = $this->getPartsByClosures(
            RendererConstant::HTML_CLOSURES_DEL[0],
            RendererConstant::HTML_CLOSURES_DEL[1],
            $oldLine
        );

        // can they not be equal, though?
        // if not, we can check $oldParts with strpos
        if (!empty($newParts) && !empty($oldParts) && (count($newParts) == count($oldParts))
        ) {
            $offset = 0;

            return preg_replace_callback(
                '/' . preg_quote(RendererConstant::HTML_CLOSURES_DEL[1], '/') . '/',
                function ($match) use ($newParts, &$offset) {
                      $replaceWith =
                      RendererConstant::HTML_CLOSURES_DEL[1] .
                      RendererConstant::HTML_CLOSURES_INS[0] .
                      $newParts[$offset++] .
                      RendererConstant::HTML_CLOSURES_INS[1];

                      return $replaceWith;
                },
                $oldLine
            );
        }

        return '';
    }

    /**
     * Get the parts of the line
     * This one is adapted from:
     * https://stackoverflow.com/a/27078384/12866913
     *
     * @param string $leftDelim  Left delimiter
     * @param string $rightDelim Right delimiter
     * @param string $line       Line
     */
    protected function getPartsByClosures(
        string $leftDelim,
        string $rightDelim,
        string $line
    ): array {
        $contents = [];
        $leftDelimLength = \strlen($leftDelim);
        $rightDelimLength = \strlen($rightDelim);
        $startFrom = $contentStart = $contentEnd = 0;

        while (($contentStart = \strpos($line, $leftDelim, $startFrom)) !== false) {
            $contentStart += $leftDelimLength;
            $contentEnd = \strpos($line, $rightDelim, $contentStart);

            if ($contentEnd === false) {
                break;
            }

            $contents[] =
                \substr(
                    $line,
                    $contentStart,
                    $contentEnd - $contentStart
                );

            $startFrom = $contentEnd + $rightDelimLength;
        }

        return $contents;
    }
}
