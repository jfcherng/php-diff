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
            ['diff', 'diff-html', 'diff-inline']
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
        $colspan = $this->options['lineNumbers'] ? '' : ' colspan="2"';

        return
            '<thead>' .
                '<tr>' .
                    (
                        $this->options['lineNumbers']
                        ?
                            '<th>' . $this->_('old_version') . '</th>' .
                            '<th>' . $this->_('new_version') . '</th>' .
                            '<th></th>'
                        :
                            ''
                    ) .
                    '<th' . $colspan . '>' . $this->_('differences') . '</th>' .
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
     * Renderer table hunks.
     *
     * @param array $hunks each hunk has many blocks
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
        foreach ($block['new']['lines'] as $no => $newLine) {
            // hmm... but there is only space for one line
            // we could only pick either the old or the new to show
            $oldLineNum = $block['old']['offset'] + $no + 1;
            $newLineNum = $block['new']['offset'] + $no + 1;

            $html .=
                '<tr data-type="=">' .
                    $this->renderLineNumberColumns($oldLineNum, $newLineNum) .
                    '<th class="sign"></th>' .
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

        foreach ($block['new']['lines'] as $no => $newLine) {
            $newLineNum = $block['new']['offset'] + $no + 1;

            $html .=
                '<tr data-type="+">' .
                    $this->renderLineNumberColumns(null, $newLineNum) .
                    '<th class="sign ins">+</th>' .
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

        foreach ($block['old']['lines'] as $no => $oldLine) {
            $oldLineNum = $block['old']['offset'] + $no + 1;

            $html .=
                '<tr data-type="-">' .
                    $this->renderLineNumberColumns($oldLineNum, null) .
                    '<th class="sign del">-</th>' .
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

        foreach ($block['old']['lines'] as $no => $oldLine) {
            $oldLineNum = $block['old']['offset'] + $no + 1;

            $html .=
                '<tr data-type="-">' .
                    $this->renderLineNumberColumns($oldLineNum, null) .
                    '<th class="sign del">-</th>' .
                    '<td class="old">' . $oldLine . '</td>' .
                '</tr>';
        }

        foreach ($block['new']['lines'] as $no => $newLine) {
            $newLineNum = $block['new']['offset'] + $no + 1;

            $html .=
                '<tr data-type="+">' .
                    $this->renderLineNumberColumns(null, $newLineNum) .
                    '<th class="sign ins">+</th>' .
                    '<td class="new">' . $newLine . '</td>' .
                '</tr>';
        }

        return $html;
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
}
