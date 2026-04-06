<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Options;

use Jfcherng\Diff\Renderer\RendererConstant;

/**
 * Value object holding all options for renderers.
 */
class RendererOptions
{
    public function __construct(
        /** Granularity of in-line diff: none|line|word|char */
        public string $detailLevel = 'line',
        /** Language code ('eng', 'cht', …) or a custom translation array. */
        public string|array $language = 'eng',
        /** Show line numbers in HTML renderers. */
        public bool $lineNumbers = true,
        /** Show a separator between diff hunks in HTML renderers. */
        public bool $separateBlock = true,
        /** Show the table header in HTML renderers. */
        public bool $showHeader = true,
        /** Convert spaces/tabs to `<span class="ch sp"> </span>` HTML tags (CSS-driven visualisation). */
        public bool $spaceToHtmlTag = false,
        /** Convert consecutive spaces to `&nbsp;` in HTML output. */
        public bool $spacesToNbsp = false,
        /** Tab width in HTML renderers. Negative = do not convert tabs to spaces. */
        public int $tabSize = 4,
        /** Combined renderer: merge replace-blocks whose changed-ratio is at or below this threshold. */
        public float $mergeThreshold = 0.8,
        /** CLI colorisation mode. See RendererConstant::CLI_COLOR_*. */
        public int $cliColorization = RendererConstant::CLI_COLOR_AUTO,
        /** JSON renderer: emit op tags as human-readable strings instead of ints. */
        public bool $outputTagAsString = false,
        /** JSON renderer: flags passed to json_encode(). */
        public int $jsonEncodeFlags = \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
        /** Word-level diff: characters that can be used to glue adjacent diff segments. */
        public array $wordGlues = ['-', ' '],
        /** Return this string verbatim when the two inputs are identical. null = renderer default. */
        public ?string $resultForIdenticals = null,
        /** Extra CSS classes added to the diff container `<div>` in HTML renderers. */
        public array $wrapperClasses = ['diff-wrapper'],
    ) {
    }

    /**
     * Convert to associative array for external consumers that still expect arrays (e.g. AbstractLineRenderer).
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'detailLevel' => $this->detailLevel,
            'language' => $this->language,
            'lineNumbers' => $this->lineNumbers,
            'separateBlock' => $this->separateBlock,
            'showHeader' => $this->showHeader,
            'spaceToHtmlTag' => $this->spaceToHtmlTag,
            'spacesToNbsp' => $this->spacesToNbsp,
            'tabSize' => $this->tabSize,
            'mergeThreshold' => $this->mergeThreshold,
            'cliColorization' => $this->cliColorization,
            'outputTagAsString' => $this->outputTagAsString,
            'jsonEncodeFlags' => $this->jsonEncodeFlags,
            'wordGlues' => $this->wordGlues,
            'resultForIdenticals' => $this->resultForIdenticals,
            'wrapperClasses' => $this->wrapperClasses,
        ];
    }

    /**
     * Create a RendererOptions from a legacy associative array, filling missing keys with defaults.
     *
     * @throws \InvalidArgumentException if any option has an invalid type
     */
    public static function fromArray(array $options): self
    {
        if (isset($options['resultForIdenticals']) && !\is_string($options['resultForIdenticals'])) {
            throw new \InvalidArgumentException('renderer option `resultForIdenticals` must be null or string.');
        }

        return new self(
            detailLevel: $options['detailLevel'] ?? 'line',
            language: $options['language'] ?? 'eng',
            lineNumbers: $options['lineNumbers'] ?? true,
            separateBlock: $options['separateBlock'] ?? true,
            showHeader: $options['showHeader'] ?? true,
            spaceToHtmlTag: $options['spaceToHtmlTag'] ?? false,
            spacesToNbsp: $options['spacesToNbsp'] ?? false,
            tabSize: $options['tabSize'] ?? 4,
            mergeThreshold: $options['mergeThreshold'] ?? 0.8,
            cliColorization: $options['cliColorization'] ?? RendererConstant::CLI_COLOR_AUTO,
            outputTagAsString: $options['outputTagAsString'] ?? false,
            jsonEncodeFlags: $options['jsonEncodeFlags'] ?? (\JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE),
            wordGlues: $options['wordGlues'] ?? ['-', ' '],
            resultForIdenticals: $options['resultForIdenticals'] ?? null,
            wrapperClasses: $options['wrapperClasses'] ?? ['diff-wrapper'],
        );
    }
}
