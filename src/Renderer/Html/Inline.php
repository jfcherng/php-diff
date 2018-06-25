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
            '<table class="diff diff-html diff-inline">' .
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
                    '<tbody class="skipped">' .
                    '<th>&hellip;</th>' .
                    '<th>&hellip;</th>' .
                    '<th class="sign">&#xA0;</th>' .
                    '<td>&#xA0;</td>' .
                    '</tbody>'
                );
            }

            foreach ($blocks as $change) {
                $html .= '<tbody class="change change-' . static::TAG_CLASS_MAP[$change['tag']] . '">';

                // equal changes should be shown on both sides of the diff
                if ($change['tag'] === SequenceMatcher::OPCODE_EQUAL) {
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;
                        $toLine = $change['changed']['offset'] + $no + 1;

                        $html .= (
                            '<tr data-type="=">' .
                            '<th class="f-num">' . $fromLine . '</th>' .
                            '<th class="t-num">' . $toLine . '</th>' .
                            '<th class="sign">&#xA0;</th>' .
                            '<td class="old">' . $line . '</td>' .
                            '</tr>'
                        );
                    }
                }
                // added lines only on the r side
                elseif ($change['tag'] === SequenceMatcher::OPCODE_INSERT) {
                    foreach ($change['changed']['lines'] as $no => $line) {
                        $toLine = $change['changed']['offset'] + $no + 1;

                        $html .= (
                            '<tr data-type="+">' .
                            '<th>&#xA0;</th>' .
                            '<th class="t-num">' . $toLine . '</th>' .
                            '<th class="sign ins">+</th>' .
                            '<td class="new"><ins>' . $line . '</ins></td>' .
                            '</tr>'
                        );
                    }
                }
                // show deleted lines only on the l side
                elseif ($change['tag'] === SequenceMatcher::OPCODE_DELETE) {
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;

                        $html .= (
                            '<tr data-type="-">' .
                            '<th class="f-num">' . $fromLine . '</th>' .
                            '<th>&#xA0;</th>' .
                            '<th class="sign del">-</th>' .
                            '<td class="old"><del>' . $line . '</del></td>' .
                            '</tr>'
                        );
                    }
                }
                // show modified lines on both sides
                elseif ($change['tag'] === SequenceMatcher::OPCODE_REPLACE) {
                    foreach ($change['base']['lines'] as $no => $line) {
                        $fromLine = $change['base']['offset'] + $no + 1;

                        $html .= (
                            '<tr data-type="-">' .
                            '<th class="f-num">' . $fromLine . '</th>' .
                            '<th>&#xA0;</th>' .
                            '<th class="sign del">-</th>' .
                            '<td class="old"><span>' . $line . '</span></td>' .
                            '</tr>'
                        );
                    }

                    foreach ($change['changed']['lines'] as $no => $line) {
                        $toLine = $change['changed']['offset'] + $no + 1;

                        $html .= (
                            '<tr data-type="+">' .
                            '<th>&#xA0;</th>' .
                            '<th class="t-num">' . $toLine . '</th>' .
                            '<th class="sign ins">+</th>' .
                            '<td class="new"><span>' . $line . '</span></td>' .
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
