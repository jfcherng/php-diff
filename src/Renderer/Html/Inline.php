<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\SequenceMatcher;

/**
 * Inline HTML diff generator.
 */
final class Inline extends AbstractHtml
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Inline',
        'type' => 'Html',
    ];

    /**
     * {@inheritdoc}
     */
    protected function renderWoker(Differ $differ): string
    {
        $changes = $this->getChanges($differ);

        if (empty($changes)) {
            return self::getIdenticalResult();
        }

        $html = '<table class="diff diff-html diff-inline">';

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
                    '<th>' . $this->_('old_version') . '</th>' .
                    '<th>' . $this->_('new_version') . '</th>' .
                    '<th></th>' .
                    '<th>' . $this->_('differences') . '</th>' .
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
        $html = '<tbody class="change change-' . self::TAG_CLASS_MAP[$change['tag']] . '">';

        switch ($change['tag']) {
            default:
            // equal changes should be shown on both sides of the diff
            case SequenceMatcher::OP_EQ:
                $html .= $this->renderTableEqual($change);
                break;
            // added lines only on the r side
            case SequenceMatcher::OP_INS:
                $html .= $this->renderTableInsert($change);
                break;
            // show deleted lines only on the l side
            case SequenceMatcher::OP_DEL:
                $html .= $this->renderTableDelete($change);
                break;
            // show modified lines on both sides
            case SequenceMatcher::OP_REP:
                $html .= $this->renderTableReplace($change);
                break;
        }

        return $html . '</tbody>';
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
            // hmm... but this is a inline template
            // we could only pick a line from the old or the new to show
            $oldLineNum = $change['old']['offset'] + $no + 1;
            $newLineNum = $change['new']['offset'] + $no + 1;

            $html .=
                '<tr data-type="=">' .
                    '<th class="n-old">' . $oldLineNum . '</th>' .
                    '<th class="n-new">' . $newLineNum . '</th>' .
                    '<th class="sign"></th>' .
                    '<td class="old">' . $oldLine . '</td>' .
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
                '<tr data-type="+">' .
                    '<th></th>' .
                    '<th class="n-new">' . $newLineNum . '</th>' .
                    '<th class="sign ins">+</th>' .
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
                '<tr data-type="-">' .
                    '<th class="n-old">' . $oldLineNum . '</th>' .
                    '<th></th>' .
                    '<th class="sign del">-</th>' .
                    '<td class="old">' . $oldLine . '</td>' .
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

        foreach ($change['old']['lines'] as $no => $oldLine) {
            $oldLineNum = $change['old']['offset'] + $no + 1;

            $html .=
                '<tr data-type="-">' .
                    '<th class="n-old">' . $oldLineNum . '</th>' .
                    '<th></th>' .
                    '<th class="sign del">-</th>' .
                    '<td class="old">' . $oldLine . '</td>' .
                '</tr>';
        }

        foreach ($change['new']['lines'] as $no => $newLine) {
            $newLineNum = $change['new']['offset'] + $no + 1;

            $html .=
                '<tr data-type="+">' .
                    '<th></th>' .
                    '<th class="n-new">' . $newLineNum . '</th>' .
                    '<th class="sign ins">+</th>' .
                    '<td class="new">' . $newLine . '</td>' .
                '</tr>';
        }

        return $html;
    }
}
