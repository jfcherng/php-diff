<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\SequenceMatcher;

/**
 * HTML Json diff generator.
 */
class JsonHtml extends AbstractHtml
{
    /**
     * {@inheritdoc}
     */
    public const array INFO = [
        'desc' => 'HTML Json',
        'type' => 'Html',
    ];

    /**
     * {@inheritdoc}
     */
    public const bool IS_TEXT_RENDERER = true;

    #[\Override]
    public function getResultForIdenticalsDefault(): string
    {
        return '[]';
    }

    #[\Override]
    protected function redererChanges(array $changes): string
    {
        if ($this->options['outputTagAsString']) {
            $this->convertTagToString($changes);
        }

        return json_encode($changes, $this->options['jsonEncodeFlags']);
    }

    /**
     * Convert tags of changes to their string form for better readability.
     *
     * @param array[][] $changes the changes
     */
    protected function convertTagToString(array &$changes): void
    {
        foreach ($changes as &$hunks) {
            foreach ($hunks as &$block) {
                $block['tag'] = SequenceMatcher::opIntToStr($block['tag']);
            }
        }
    }

    #[\Override]
    protected function formatStringFromLines(string $string): string
    {
        return $this->htmlSafe($string);
    }
}
