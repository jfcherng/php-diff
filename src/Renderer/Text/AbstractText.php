<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Text;

use Jfcherng\Diff\Renderer\AbstractRenderer;

/**
 * Base renderer for rendering text-based diffs.
 */
abstract class AbstractText extends AbstractRenderer
{
    /**
     * @var bool is this renderer pure text?
     */
    const IS_TEXT_RENDERER = true;

    /**
     * {@inheritdoc}
     */
    public function getResultForIdenticalsDefault(): string
    {
        return '';
    }
	
	/**
     * {@inheritdoc}
     */
    public function arrayRenderWoker(array $differArray): string
    {
        return '';
    }
	
	/**
     * {@inheritdoc}
     */
    public function baseWoker(array $changes): string
    {
        return '';
    }
}
