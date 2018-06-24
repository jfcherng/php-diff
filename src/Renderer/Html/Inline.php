<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\Utility\SequenceMatcher;

/**
 * Inline HTML diff generator.
 */
class Inline extends AbstractHtml
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
        $options = $this->diff->options;
        $changes = $this->getChanges();

        if (empty($changes)) {
            return static::IDENTICAL_RESULT;
        }

        $html = '';

        $html .= (
            '<table class="Differences DifferencesInline">' .
            '<thead>' .
            '<tr>' .
            '<th>' . $this->_('old_version') . '</th>' .
            '<th>' . $this->_('new_version') . '</th>' .
            '<th>&#xA0;</th>' .
            '<th>' . $this->_('differences') . '</th>' .
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
                    '<th>&hellip;</th>' .
                    '<th>&hellip;</th>' .
                    '<th class="Sign">&#xA0;</th>' .
                    '<td>&#xA0;</td>' .
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
                            '<tr diff-type="=">' .
                            '<th class="fNum">' . $fromLine . '</th>' .
                            '<th class="tNum">' . $toLine . '</th>' .
                            '<th class="Sign">&#xA0;</th>' .
                            '<td class="Left">' . $line . '</td>' .
                            '</tr>'
                        );
                    }
                }
                // added lines only on the right side
                elseif ($change['tag'] === SequenceMatcher::OPCODE_INSERT) {
                    foreach ($change['changed']['lines'] as $no => $line) {
                        $toLine = $change['changed']['offset'] + $no + 1;

                        $html .= (
                            '<tr diff-type="+">' .
                            '<th>&#xA0;</th>' .
                            '<th class="tNum">' . $toLine . '</th>' .
                            '<th class="Sign ins">+</th>' .
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
                            '<tr diff-type="-">' .
                            '<th class="fNum">' . $fromLine . '</th>' .
                            '<th>&#xA0;</th>' .
                            '<th class="Sign del">-</th>' .
                            '<td class="Left"><del>' . $line . '</del></td>' .
                            '</tr>'
                        );
                    }
                }
                // show modified lines on both sides
                elseif ($change['tag'] === SequenceMatcher::OPCODE_REPLACE) {
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;

                        $html .= (
                            '<tr diff-type="-">' .
                            '<th class="fNum">' . $fromLine . '</th>' .
                            '<th>&#xA0;</th>' .
                            '<th class="Sign del">-</th>' .
                            '<td class="Left"><span>' . $line . '</span></td>' .
                            '</tr>'
                        );
                    }

                    foreach ($change['changed']['lines'] as $no => $line) {
                        $toLine = $change['changed']['offset'] + $no + 1;

                        $html .= (
                            '<tr diff-type="+">' .
                            '<th>&#xA0;</th>' .
                            '<th class="tNum">' . $toLine . '</th>' .
                            '<th class="Sign ins">+</th>' .
                            '<td class="Right"><span>' . $line . '</span></td>' .
                            '</tr>'
                        );
                    }
                }

                $html .= '</tbody>';
            }
        }

        $html .= '</table>';

        return $html;
    }
}
