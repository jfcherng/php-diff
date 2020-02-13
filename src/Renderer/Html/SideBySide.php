<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\SequenceMatcher;

/**
 * Side by Side HTML diff generator.
 */
final class SideBySide extends AbstractHtml
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Side by side',
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
            ['diff', 'diff-html', 'diff-side-by-side']
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
        $colspan = $this->options['lineNumbers'] ? ' colspan="2"' : '';

        return
            '<thead>' .
                '<tr>' .
                    '<th' . $colspan . '>' . $this->_('old_version') . '</th>' .
                    '<th' . $colspan . '>' . $this->_('new_version') . '</th>' .
                '</tr>' .
            '</thead>';
    }

    /**
     * Renderer the table separate block.
     */
    protected function renderTableSeparateBlock(): string
    {
        $colspan = $this->options['lineNumbers'] ? '4' : '2';

        return
            '<tbody class="skipped">' .
                '<tr>' .
                    '<td colspan="' . $colspan . '"></td>' .
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
            $newLine = $change['new']['lines'][$no];

            if ($this->options['lineNumbers']) {
                $oldLineNum = $change['old']['offset'] + $no + 1;
                $newLineNum = $change['new']['offset'] + $no + 1;

                $html .=
                    '<tr>' .
                        $this->renderLineNumberColumn('old', $oldLineNum) .
                        '<td class="old">' . $oldLine . '</td>' .
                        $this->renderLineNumberColumn('new', $newLineNum) .
                        '<td class="new">' . $newLine . '</td>' .
                    '</tr>';
            } else {
                $html .=
                    '<tr>' .
                        '<td class="new" colspan="2">' . $newLine . '</td>' .
                    '</tr>';
            }
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
            if ($this->options['lineNumbers']) {
                $newLineNum = $change['new']['offset'] + $no + 1;

                $html .=
                    '<tr>' .
                        $this->renderLineNumberColumn('', null) .
                        '<td class="old"></td>' .
                        $this->renderLineNumberColumn('new', $newLineNum) .
                        '<td class="new">' . $newLine . '</td>' .
                    '</tr>';
            } else {
                $html .=
                    '<tr>' .
                        '<td class="new" colspan="2">' . $newLine . '</td>' .
                    '</tr>';
            }
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
            if ($this->options['lineNumbers']) {
                $oldLineNum = $change['old']['offset'] + $no + 1;

                $html .=
                    '<tr>' .
                        $this->renderLineNumberColumn('old', $oldLineNum) .
                        '<td class="old">' . $oldLine . '</td>' .
                        $this->renderLineNumberColumn('', null) .
                        '<td class="new"></td>' .
                    '</tr>';
            } else {
                $html .=
                    '<tr>' .
                        '<td class="old" colspan="2">' . $oldLine . '</td>' .
                    '</tr>';
            }
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

        if (\count($change['old']['lines']) >= \count($change['new']['lines'])) {
            foreach ($change['old']['lines'] as $no => $oldLine) {
                $oldLineNum = $change['old']['offset'] + $no + 1;

                if (isset($change['new']['lines'][$no])) {
                    $newLineNum = $change['new']['offset'] + $no + 1;
                    $newLine = '<span>' . $change['new']['lines'][$no] . '</span>';
                } else {
                    $newLineNum = null;
                    $newLine = '';
                }

                $html .=
                    '<tr>' .
                        $this->renderLineNumberColumn('old', $oldLineNum) .
                        '<td class="old"><span>' . $oldLine . '</span></td>' .
                        $this->renderLineNumberColumn('new', $newLineNum) .
                        '<td class="new">' . $newLine . '</td>' .
                    '</tr>';
            }
        } else {
            foreach ($change['new']['lines'] as $no => $newLine) {
                $newLineNum = $change['new']['offset'] + $no + 1;

                if (isset($change['old']['lines'][$no])) {
                    $oldLineNum = $change['old']['offset'] + $no + 1;
                    $oldLine = '<span>' . $change['old']['lines'][$no] . '</span>';
                } else {
                    $oldLineNum = null;
                    $oldLine = '';
                }

                $html .=
                    '<tr>' .
                        $this->renderLineNumberColumn('old', $oldLineNum) .
                        '<td class="old"><span>' . $oldLine . '</span></td>' .
                        $this->renderLineNumberColumn('new', $newLineNum) .
                        '<td class="new">' . $newLine . '</td>' .
                    '</tr>';
            }
        }

        return $html;
    }

    /**
     * Renderer the line number column.
     *
     * @param string   $type    The diff type
     * @param null|int $lineNum The line number
     */
    protected function renderLineNumberColumn(string $type, ?int $lineNum): string
    {
        if (!$this->options['lineNumbers']) {
            return '';
        }

        return isset($lineNum)
            ? '<th class="n-' . $type . '">' . $lineNum . '</th>'
            : '<th></th>';
    }
}
