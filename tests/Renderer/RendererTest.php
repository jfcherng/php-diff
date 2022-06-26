<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test\Renderer;

use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Exception\UnsupportedFunctionException;
use Jfcherng\Diff\Factory\RendererFactory;
use Jfcherng\Diff\Renderer\AbstractRenderer;
use Jfcherng\Diff\Utility\Language;
use PHPUnit\Framework\TestCase;

/**
 * Test general methods from the AbstractRenderer.
 *
 * @coversNothing
 *
 * @internal
 */
final class RendererTest extends TestCase
{
    /**
     * Test the AbstractRenderer::setOptions with language array.
     *
     * @covers \Jfcherng\Diff\Renderer\AbstractRenderer::setOptions
     */
    public function testSetOptionsWithLanguageArray(): void
    {
        $testMarker = '_TEST_MARKER_';

        $languageArrayDefault = (new Language('eng'))->getTranslations();
        $languageArrayTest = ['differences' => $testMarker] + $languageArrayDefault;

        $diffResult = DiffHelper::calculate(
            'foo',
            'bar',
            'Inline',
            [],
            ['language' => $languageArrayTest],
        );

        static::assertStringContainsString(
            $testMarker,
            $diffResult,
            'Rederer options: "language" array should work.',
        );
    }

    /**
     * Test the AbstractRenderer::setOptions with result for identicals.
     *
     * @covers \Jfcherng\Diff\Renderer\AbstractRenderer::setOptions
     */
    public function testSetOptionsWithResultForIdenticals(): void
    {
        $testMarker = '_TEST_MARKER_';

        $diffResult = DiffHelper::calculate(
            'we are the same',
            'we are the same',
            'Inline',
            [],
            ['resultForIdenticals' => $testMarker],
        );

        static::assertSame(
            $testMarker,
            $diffResult,
            'Rederer options: result for identicals should work.',
        );
    }

    /**
     * Test the AbstractRenderer::setOptions with an invalid result for identicals.
     *
     * @covers \Jfcherng\Diff\Renderer\AbstractRenderer::setOptions
     */
    public function testSetOptionsWithInvalidResultForIdenticals(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $diffResult = DiffHelper::calculate(
            'we are the same',
            'we are the same',
            'Inline',
            [],
            ['resultForIdenticals' => 50 /* should be string */],
        );
    }

    /**
     * Test HTML renderers should be able to render with JSON renderer's result.
     *
     * @covers \Jfcherng\Diff\Renderer\AbstractRenderer::renderArray
     */
    public function testHtmlRendererRenderWithResultFromJsonRenderer(): void
    {
        static $rendererNames = ['Inline', 'SideBySide', 'Combined', 'JsonHtml'];

        $old = '_TEST_MARKER_OLD_';
        $new = '_TEST_MARKER_NEW_';
        $differOptions = [];
        $rendererOptions = [];

        foreach ($rendererNames as $rendererName) {
            $renderer = RendererFactory::make($rendererName, $rendererOptions);

            $goldenResult = DiffHelper::calculate(
                $old,
                $new,
                $rendererName,
                $differOptions,
                $rendererOptions,
            );

            // test "outputTagAsString" is false
            $jsonResult = DiffHelper::calculate(
                $old,
                $new,
                'JsonHtml',
                $differOptions,
                ['outputTagAsString' => false] + $rendererOptions,
            );

            static::assertSame(
                $goldenResult,
                $renderer->renderArray(json_decode($jsonResult, true)),
                "HTML renderers should be able to render with JSON result. ('outputTagAsString' => false)",
            );

            // test "outputTagAsString" is true
            $jsonResult = DiffHelper::calculate(
                $old,
                $new,
                'JsonHtml',
                $differOptions,
                ['outputTagAsString' => true] + $rendererOptions,
            );

            static::assertSame(
                $goldenResult,
                $renderer->renderArray(json_decode($jsonResult, true)),
                "HTML renderers should be able to render with JSON result. ('outputTagAsString' => true)",
            );
        }
    }

    /**
     * Test text renderers are not able to render with JSON renderer's result.
     *
     * @covers \Jfcherng\Diff\Renderer\AbstractRenderer::renderArray
     */
    public function testTextRendererRenderWithResultFromJsonRenderer(): void
    {
        $this->expectException(UnsupportedFunctionException::class);

        $jsonResult = DiffHelper::calculate('_TEST_MARKER_OLD_', '_TEST_MARKER_NEW_', 'JsonHtml');

        $textRenderer = RendererFactory::make('Unified');
        $UnifiedResult = $textRenderer->renderArray(json_decode($jsonResult, true));
    }
}
