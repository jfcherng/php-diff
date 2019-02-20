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

        foreach ($change['old']['lines'] as $no => $line) {
            $oldLineNum = $change['old']['offset'] + $no + 1;
            $newLine = $change['new']['offset'] + $no + 1;

            $html .=
                '<tr>' .
                    '<th class="n-old">' . $oldLineNum . '</th>' .
                    '<td class="old">' . $line . '</td>' .
                    '<th class="n-new">' . $newLine . '</th>' .
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
