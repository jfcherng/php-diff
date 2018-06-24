<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Utility\SequenceMatcher;

/**
 * Side by Side HTML diff generator.
 */
class SideBySide extends AbstractHtml
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
        $options = $this->diff->options;
        $changes = $this->getChanges();

        if (empty($changes)) {
            return static::IDENTICAL_RESULT;
        }

        $html = '';

        $html .= (
            '<table class="Differences DifferencesSideBySide">' .
            '<thead>' .
            '<tr>' .
            '<th colspan="2">' . $this->_('old_version') . '</th>' .
            '<th colspan="2">' . $this->_('new_version') . '</th>' .
            '</tr>' .
            '</thead>'
        );

        foreach ($changes as $i => $blocks) {
            // If this is a separate block, we're condensing code so output...,
            // indicating a significant portion of the code has been collapsed as
            // it is the same
            if ($i > 0 && $options['separateBlock']) {
                $html .= (
                    '<tbody class="Skipped">' .
                    '<th>&hellip;</th><td>&#xA0;</td>' .
                    '<th>&hellip;</th><td>&#xA0;</td>' .
                    '</tbody>'
                );
            }

            foreach ($blocks as $change) {
                $html .= '<tbody class="Change' . static::TAG_CLASS_MAP[$change['tag']] . '">';

                // equal changes should be shown on both sides of the diff
                if ($change['tag'] === SequenceMatcher::OPCODE_EQUAL) {
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;
                        $toLine = $change['changed']['offset'] + $no + 1;

                        $html .= (
                            '<tr>' .
                            '<th class="fNum">' . $fromLine . '</th>' .
                            '<td class="Left"><span>' . $line . '</span></td>' .
                            '<th class="tNum">' . $toLine . '</th>' .
                            '<td class="Right"><span>' . $line . '</span></td>' .
                            '</tr>'
                        );
                    }
                }
                // added lines only on the right side
                elseif ($change['tag'] === SequenceMatcher::OPCODE_INSERT) {
                    foreach ($change['changed']['lines'] as $no => $line) {
                        $toLine = $change['changed']['offset'] + $no + 1;

                        $html .= (
                            '<tr>' .
                            '<th>&#xA0;</th>' .
                            '<td class="Left">&#xA0;</td>' .
                            '<th class="tNum">' . $toLine . '</th>' .
                            '<td class="Right"><ins>' . $line . '</ins></td>' .
                            '</tr>'
                        );
                    }
                }
                // show deleted lines only on the left side
                elseif ($change['tag'] === SequenceMatcher::OPCODE_DELETE) {
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;

                        $html .= (
                            '<tr>' .
                            '<th class="fNum">' . $fromLine . '</th>' .
                            '<td class="Left"><del>' . $line . '</del></td>' .
                            '<th>&#xA0;</th>' .
                            '<td class="Right">&#xA0;</td>' .
                            '</tr>'
                        );
                    }
                }
                // show modified lines on both sides
                elseif ($change['tag'] === SequenceMatcher::OPCODE_REPLACE) {
                    if (count($change['base']['lines']) >= count($change['changed']['lines'])) {
                        foreach ($change['base']['lines'] as $no => $line) {
                            $fromLine = $change['base']['offset'] + $no + 1;

                            if (isset($change['changed']['lines'][$no])) {
                                $toLine = $change['base']['offset'] + $no + 1;
                                $changedLine = '<span>' . $change['changed']['lines'][$no] . '</span>';
                            } else {
                                $toLine = '&#xA0;';
                                $changedLine = '&#xA0;';
                            }

                            $html .= (
                                '<tr>' .
                                '<th class="fNum">' . $fromLine . '</th>' .
                                '<td class="Left"><span>' . $line . '</span></td>' .
                                '<th class="tNum">' . $toLine . '</th>' .
                                '<td class="Right">' . $changedLine . '</td>' .
                                '</tr>'
                            );
                        }
                    } else {
                        foreach ($change['changed']['lines'] as $no => $changedLine) {
                            $toLine = $change['changed']['offset'] + $no + 1;

                            if (isset($change['base']['lines'][$no])) {
                                $fromLine = $change['base']['offset'] + $no + 1;
                                $line = '<span>' . $change['base']['lines'][$no] . '</span>';
                            } else {
                                $fromLine = '&#xA0;';
                                $line = '&#xA0;';
                            }

                            $html .= (
                                '<tr>' .
                                '<th class="fNum">' . $fromLine . '</th>' .
                                '<td class="Left"><span>' . $line . '</span></td>' .
                                '<th class="tNum">' . $toLine . '</th>' .
                                '<td class="Right">' . $changedLine . '</td>' .
                                '</tr>'
                            );
                        }
                    }
                }

                $html .= '</tbody>';
            }
        }

        $html .= '</table>';

        return $html;
    }
}
