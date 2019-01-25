<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Utility\SequenceMatcher;

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

        $html = '<table class="diff diff-html diff-side-by-side">';

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
                '<tr>' .
                    '<th class="f-num">' . $fromLine . '</th>' .
                    '<td class="old">' . $line . '</td>' .
                    '<th class="t-num">' . $toLine . '</th>' .
                    '<td class="new">' . $line . '</td>' .
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
                '<tr>' .
                    '<th></th>' .
                    '<td class="old"></td>' .
                    '<th class="t-num">' . $toLine . '</th>' .
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
                '<tr>' .
                    '<th class="f-num">' . $fromLine . '</th>' .
                    '<td class="old"><del>' . $line . '</del></td>' .
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

        if (\count($change['base']['lines']) >= \count($change['changed']['lines'])) {
            foreach ($change['base']['lines'] as $no => $line) {
                $fromLine = $change['base']['offset'] + $no + 1;

                if (isset($change['changed']['lines'][$no])) {
                    $toLine = $change['base']['offset'] + $no + 1;
                    $changedLine = '<span>' . $change['changed']['lines'][$no] . '</span>';
                } else {
                    $toLine = '';
                    $changedLine = '';
                }

                $html .=
                    '<tr>' .
                        '<th class="f-num">' . $fromLine . '</th>' .
                        '<td class="old"><span>' . $line . '</span></td>' .
                        '<th class="t-num">' . $toLine . '</th>' .
                        '<td class="new">' . $changedLine . '</td>' .
                    '</tr>';
            }
        } else {
            foreach ($change['changed']['lines'] as $no => $changedLine) {
                $toLine = $change['changed']['offset'] + $no + 1;

                if (isset($change['base']['lines'][$no])) {
                    $fromLine = $change['base']['offset'] + $no + 1;
                    $line = '<span>' . $change['base']['lines'][$no] . '</span>';
                } else {
                    $fromLine = '';
                    $line = '';
                }

                $html .=
                    '<tr>' .
                        '<th class="f-num">' . $fromLine . '</th>' .
                        '<td class="old"><span>' . $line . '</span></td>' .
                        '<th class="t-num">' . $toLine . '</th>' .
                        '<td class="new">' . $changedLine . '</td>' .
                    '</tr>';
            }
        }

        return $html;
    }
}
