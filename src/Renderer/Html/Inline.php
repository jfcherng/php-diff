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
        $colspan = (!$this->options['lineNumbers'] ? ' colspan="2"' : '');

        return
            '<thead>' .
                '<tr>' .
                    ($this->options['lineNumbers'] ?
                        '<th>' . $this->_('old_version') . '</th>' .
                        '<th>' . $this->_('new_version') . '</th>' .
                        '<th></th>'
                        : ''
                    ) .
                    '<th'.$colspan.'>' . $this->_('differences') . '</th>' .
                '</tr>' .
            '</thead>';
    }

    /**
     * Renderer the table separate block.
     */
    protected function renderTableSeparateBlock(): string
    {
        $colspan = (!$this->options['lineNumbers'] ? '2' : '4');

        return
            '<tbody class="skipped">' .
                '<tr>' .
                    '<td colspan="'.$colspan.'"></td>' .
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
            if ($this->options['lineNumbers']) {
                // hmm... but this is a inline renderer
                // we could only pick a line from the old or the new to show
                $oldLineNum = $change['old']['offset'] + $no + 1;
                $newLineNum = $change['new']['offset'] + $no + 1;
            }

            $html .=
                '<tr data-type="=">' .
                    ($this->options['lineNumbers'] ?
                        '<th class="n-old">' . $oldLineNum . '</th>' .
                        '<th class="n-old">' . $newLineNum . '</th>'
                        : ''
                    ) .
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
     */
    protected function renderTableInsert(array $change): string
    {
        $html = '';

        foreach ($change['new']['lines'] as $no => $newLine) {
            if ($this->options['lineNumbers']) {
                $newLineNum = $change['new']['offset'] + $no + 1;
            }

            $html .=
                '<tr data-type="+">' .
                    ($this->options['lineNumbers'] ?
                        '<th></th>' .
                        '<th class="n-new">' . $newLineNum . '</th>'
                        : ''
                    ) .
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
     */
    protected function renderTableDelete(array $change): string
    {
        $html = '';

        foreach ($change['old']['lines'] as $no => $oldLine) {
            if ($this->options['lineNumbers']) {
                $oldLineNum = $change['old']['offset'] + $no + 1;
            }

            $html .=
                '<tr data-type="-">' .
                    ($this->options['lineNumbers'] ?
                        '<th class="n-old">' . $oldLineNum . '</th>' .
                        '<th></th>'
                        : ''
                    ) .
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
     */
    protected function renderTableReplace(array $change): string
    {
        $html = '';

        foreach ($change['old']['lines'] as $no => $oldLine) {
            if ($this->options['lineNumbers']) {
                $oldLineNum = $change['old']['offset'] + $no + 1;
            }

            $html .=
                '<tr data-type="-">' .
                    ($this->options['lineNumbers'] ?
                        '<th class="n-old">' . $oldLineNum . '</th>' .
                        '<th></th>'
                        : ''
                    ) .
                    '<th class="sign del">-</th>' .
                    '<td class="old">' . $oldLine . '</td>' .
                '</tr>';
        }

        foreach ($change['new']['lines'] as $no => $newLine) {
            if ($this->options['lineNumbers']) {
                $newLineNum = $change['new']['offset'] + $no + 1;
            }

            $html .=
                '<tr data-type="+">' .
                    ($this->options['lineNumbers'] ?
                        '<th></th>' .
                        '<th class="n-new">' . $newLineNum . '</th>'
                        : ''
                    ) .
                    '<th class="sign ins">+</th>' .
                    '<td class="new">' . $newLine . '</td>' .
                '</tr>';
        }

        return $html;
    }
}
