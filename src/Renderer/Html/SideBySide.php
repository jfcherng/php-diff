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
            $oldLine = $block['old']['lines'][$no];

            $oldLineNum = $block['old']['offset'] + $no + 1;
            $newLineNum = $block['new']['offset'] + $no + 1;

            $html .=
                '<tr>' .
                    $this->renderLineNumberColumn('old', $oldLineNum) .
                    '<td class="old">' . $oldLine . '</td>' .
                    $this->renderLineNumberColumn('new', $newLineNum) .
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
                '<tr>' .
                    $this->renderLineNumberColumn('', null) .
                    '<td class="old"></td>' .
                    $this->renderLineNumberColumn('new', $newLineNum) .
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
                '<tr>' .
                    $this->renderLineNumberColumn('old', $oldLineNum) .
                    '<td class="old">' . $oldLine . '</td>' .
                    $this->renderLineNumberColumn('', null) .
                    '<td class="new"></td>' .
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

        $lineCountMax = \max(\count($block['old']['lines']), \count($block['new']['lines']));

        for ($no = 0; $no < $lineCountMax; ++$no) {
            if (isset($block['old']['lines'][$no])) {
                $oldLineNum = $block['old']['offset'] + $no + 1;
                $oldLine = $block['old']['lines'][$no];
            } else {
                $oldLineNum = null;
                $oldLine = '';
            }

            if (isset($block['new']['lines'][$no])) {
                $newLineNum = $block['new']['offset'] + $no + 1;
                $newLine = $block['new']['lines'][$no];
            } else {
                $newLineNum = null;
                $newLine = '';
            }

            $html .=
                '<tr>' .
                    $this->renderLineNumberColumn('old', $oldLineNum) .
                    '<td class="old">' . $oldLine . '</td>' .
                    $this->renderLineNumberColumn('new', $newLineNum) .
                    '<td class="new">' . $newLine . '</td>' .
                '</tr>';
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
