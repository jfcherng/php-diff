<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

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
    ];

    /**
     * {@inheritdoc}
     */
    public function render(): string
    {
        $changes = $this->getChanges();

        if (empty($changes)) {
            return self::IDENTICAL_RESULT;
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
            case SequenceMatcher::OPCODE_EQUAL:
                $html .= $this->renderTableEqual($change);
                break;
            // added lines only on the r side
            case SequenceMatcher::OPCODE_INSERT:
                $html .= $this->renderTableInsert($change);
                break;
            // show deleted lines only on the l side
            case SequenceMatcher::OPCODE_DELETE:
                $html .= $this->renderTableDelete($change);
                break;
            // show modified lines on both sides
            case SequenceMatcher::OPCODE_REPLACE:
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

        foreach ($change['base']['lines'] as $no => $line) {
            $fromLine = $change['base']['offset'] + $no + 1;
            $toLine = $change['changed']['offset'] + $no + 1;

            $html .=
                '<tr data-type="=">' .
                    '<th class="f-num">' . $fromLine . '</th>' .
                    '<th class="t-num">' . $toLine . '</th>' .
                    '<th class="sign"></th>' .
                    '<td class="old">' . $line . '</td>' .
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

        foreach ($change['changed']['lines'] as $no => $line) {
            $toLine = $change['changed']['offset'] + $no + 1;

            $html .=
                '<tr data-type="+">' .
                    '<th></th>' .
                    '<th class="t-num">' . $toLine . '</th>' .
                    '<th class="sign ins">+</th>' .
                    '<td class="new"><ins>' . $line . '</ins></td>' .
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

        foreach ($change['base']['lines'] as $no => $line) {
            $fromLine = $change['base']['offset'] + $no + 1;

            $html .=
                '<tr data-type="-">' .
                    '<th class="f-num">' . $fromLine . '</th>' .
                    '<th></th>' .
                    '<th class="sign del">-</th>' .
                    '<td class="old"><del>' . $line . '</del></td>' .
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

        foreach ($change['base']['lines'] as $no => $line) {
            $fromLine = $change['base']['offset'] + $no + 1;

            $html .=
                '<tr data-type="-">' .
                    '<th class="f-num">' . $fromLine . '</th>' .
                    '<th></th>' .
                    '<th class="sign del">-</th>' .
                    '<td class="old">' . $line . '</td>' .
                '</tr>';
        }

        foreach ($change['changed']['lines'] as $no => $line) {
            $toLine = $change['changed']['offset'] + $no + 1;

            $html .=
                '<tr data-type="+">' .
                    '<th></th>' .
                    '<th class="t-num">' . $toLine . '</th>' .
                    '<th class="sign ins">+</th>' .
                    '<td class="new">' . $line . '</td>' .
                '</tr>';
        }

        return $html;
    }
}
