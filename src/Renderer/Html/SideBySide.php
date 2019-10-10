<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Differ;
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
    protected function renderWoker(Differ $differ): string
    {
        $changes = $this->getChanges($differ);

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
     *
     * @return string
     */
    protected function renderTableHeader(): string
    {
        return
            '<thead>' .
                '<tr>' .
                    '<th colspan="2">' . $this->_('old_version') . '</th>' .
                    '<th colspan="2">' . $this->_('new_version') . '</th>' .
                '</tr>' .
            '</thead>';
    }

    /**
     * Renderer the table separate block.
     *
     * @return string
     */
    protected function renderTableSeparateBlock(): string
    {
        return
            '<tbody class="skipped">' .
                '<tr>' .
                    '<td colspan="4"></td>' .
                '</tr>' .
            '</tbody>';
    }

    /**
     * Renderer the table block.
     *
     * @param array $change the change
     *
     * @return string
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
     *
     * @return string
     */
    protected function renderTableEqual(array $change): string
    {
        $html = '';

        // note that although we are in a OP_EQ situation,
        // the old and the new may not be exactly the same
        // because of ignoreCase, ignoreWhitespace, etc
        foreach ($change['old']['lines'] as $no => $oldLine) {
            $newLine = $change['new']['lines'][$no];

            $oldLineNum = $change['old']['offset'] + $no + 1;
            $newLineNum = $change['new']['offset'] + $no + 1;

            $html .=
                '<tr>' .
                    '<th class="n-old">' . $oldLineNum . '</th>' .
                    '<td class="old">' . $oldLine . '</td>' .
                    '<th class="n-new">' . $newLineNum . '</th>' .
                    '<td class="new">' . $newLine . '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: insert.
     *
     * @param array $change the change
     *
     * @return string
     */
    protected function renderTableInsert(array $change): string
    {
        $html = '';

        foreach ($change['new']['lines'] as $no => $newLine) {
            $newLineNum = $change['new']['offset'] + $no + 1;

            $html .=
                '<tr>' .
                    '<th></th>' .
                    '<td class="old"></td>' .
                    '<th class="n-new">' . $newLineNum . '</th>' .
                    '<td class="new">' . $newLine . '</td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: delete.
     *
     * @param array $change the change
     *
     * @return string
     */
    protected function renderTableDelete(array $change): string
    {
        $html = '';

        foreach ($change['old']['lines'] as $no => $oldLine) {
            $oldLineNum = $change['old']['offset'] + $no + 1;

            $html .=
                '<tr>' .
                    '<th class="n-old">' . $oldLineNum . '</th>' .
                    '<td class="old">' . $oldLine . '</td>' .
                    '<th></th>' .
                    '<td class="new"></td>' .
                '</tr>';
        }

        return $html;
    }

    /**
     * Renderer the table block: replace.
     *
     * @param array $change the change
     *
     * @return string
     */
    protected function renderTableReplace(array $change): string
    {
        $html = '';

        if (\count($change['old']['lines']) >= \count($change['new']['lines'])) {
            foreach ($change['old']['lines'] as $no => $oldLine) {
                $oldLineNum = $change['old']['offset'] + $no + 1;

                if (isset($change['new']['lines'][$no])) {
                    $newLineNum = $change['old']['offset'] + $no + 1;
                    $newLine = '<span>' . $change['new']['lines'][$no] . '</span>';
                } else {
                    $newLineNum = '';
                    $newLine = '';
                }

                $html .=
                    '<tr>' .
                        '<th class="n-old">' . $oldLineNum . '</th>' .
                        '<td class="old"><span>' . $oldLine . '</span></td>' .
                        '<th class="n-new">' . $newLineNum . '</th>' .
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
                    $oldLineNum = '';
                    $oldLine = '';
                }

                $html .=
                    '<tr>' .
                        '<th class="n-old">' . $oldLineNum . '</th>' .
                        '<td class="old"><span>' . $oldLine . '</span></td>' .
                        '<th class="n-new">' . $newLineNum . '</th>' .
                        '<td class="new">' . $newLine . '</td>' .
                    '</tr>';
            }
        }

        return $html;
    }
}
