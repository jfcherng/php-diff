<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

use Jfcherng\Diff\Contract\Renderer\AbstractHtmlRenderer;
use Jfcherng\Diff\Contract\Renderer\RendererTypeEnum;
use Jfcherng\Diff\SequenceMatcher;

/**
 * HTML Json diff generator.
 */
class JsonHtml extends AbstractHtmlRenderer
{
    /**
     * {@inheritdoc}
     */
    public const INFO = [
        'desc' => 'HTML Json',
        'type' => RendererTypeEnum::Html,
    ];

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
    protected function renderChanges(array $changes): string
    {
        if ($this->options['outputOpAsString']) {
            $this->convertOpToString($changes);
        }

        return json_encode($changes, $this->options['jsonEncodeFlags']);
    }

    /**
     * Convert ops of changes to their string form for better readability.
     *
     * @param array[][] $changes the changes
     */
    protected function convertOpToString(array &$changes): void
    {
        foreach ($changes as &$hunks) {
            foreach ($hunks as &$block) {
                $block['op'] = SequenceMatcher::opIntToStr($block['op']);
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
