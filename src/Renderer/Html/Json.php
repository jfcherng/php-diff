<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html;

use Jfcherng\Diff\SequenceMatcher;

/**
 * Json diff generator.
 */
final class Json extends AbstractHtml
{
    /**
     * {@inheritdoc}
     */
    const INFO = [
        'desc' => 'Json',
        'type' => 'Html',
    ];

    /**
     * {@inheritdoc}
     */
    const IS_TEXT_RENDERER = true;

    /**
     * {@inheritdoc}
     */
    public function getResultForIdenticalsDefault(): string
    {
        return '[]';
    }

    /**
     * {@inheritdoc}
     */
    protected function redererChanges(array $changes): string
    {
        if ($this->options['outputTagAsString']) {
            $this->convertTagToString($changes);
        }

        return \json_encode(
            $changes,
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Convert tags of changes to their string form for better readability.
     *
     * @param array $changes the changes
     */
    protected function convertTagToString(array &$changes): void
    {
        foreach ($changes as &$blocks) {
            foreach ($blocks as &$change) {
                $change['tag'] = SequenceMatcher::opIntToStr($change['tag']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function formatStringFromLines(string $string): string
    {
        return $this->htmlSafe($string);
    }
}
