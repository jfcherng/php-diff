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
    ];

    /**
     * {@inheritdoc}
     */
    public function render(): string
    {
        $changes = $this->getChanges();

        if (empty($changes)) {
            return self::getIdenticalResult();
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
        foreach ($change['base']['lines'] as $no => $oldLine) {
            $newLine = $change['changed']['lines'][$no];

            $oldLineNum = $change['base']['offset'] + $no + 1;
            $newLineNum = $change['changed']['offset'] + $no + 1;

            $html .=
                '<tr>' .
                    '<th class="f-num">' . $oldLineNum . '</th>' .
                    '<td class="old">' . $oldLine . '</td>' .
                    '<th class="t-num">' . $newLineNum . '</th>' .
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

        foreach ($change['changed']['lines'] as $no => $newLine) {
            $newLineNum = $change['changed']['offset'] + $no + 1;

            $html .=
                '<tr>' .
                    '<th></th>' .
                    '<td class="old"></td>' .
                    '<th class="t-num">' . $newLineNum . '</th>' .
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

        foreach ($change['base']['lines'] as $no => $oldLine) {
            $oldLineNum = $change['base']['offset'] + $no + 1;

            $html .=
                '<tr>' .
                    '<th class="f-num">' . $oldLineNum . '</th>' .
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

        if (\count($change['base']['lines']) >= \count($change['changed']['lines'])) {
            foreach ($change['base']['lines'] as $no => $oldLine) {
                $oldLineNum = $change['base']['offset'] + $no + 1;

                if (isset($change['changed']['lines'][$no])) {
                    $newLineNum = $change['base']['offset'] + $no + 1;
                    $newLine = '<span>' . $change['changed']['lines'][$no] . '</span>';
                } else {
                    $newLineNum = '';
                    $newLine = '';
                }

                $html .=
                    '<tr>' .
                        '<th class="f-num">' . $oldLineNum . '</th>' .
                        '<td class="old"><span>' . $oldLine . '</span></td>' .
                        '<th class="t-num">' . $newLineNum . '</th>' .
                        '<td class="new">' . $newLine . '</td>' .
                    '</tr>';
            }
        } else {
            foreach ($change['changed']['lines'] as $no => $newLine) {
                $newLineNum = $change['changed']['offset'] + $no + 1;

                if (isset($change['base']['lines'][$no])) {
                    $oldLineNum = $change['base']['offset'] + $no + 1;
                    $oldLine = '<span>' . $change['base']['lines'][$no] . '</span>';
                } else {
                    $oldLineNum = '';
                    $oldLine = '';
                }

                $html .=
                    '<tr>' .
                        '<th class="f-num">' . $oldLineNum . '</th>' .
                        '<td class="old"><span>' . $oldLine . '</span></td>' .
                        '<th class="t-num">' . $newLineNum . '</th>' .
                        '<td class="new">' . $newLine . '</td>' .
                    '</tr>';
            }
        }

        return $html;
    }
}
