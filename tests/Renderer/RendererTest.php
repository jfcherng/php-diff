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
            ['language' => $languageArrayTest]
        );

        static::assertStringContainsString(
            $testMarker,
            $diffResult,
            'Rederer options: "language" array should work.'
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
            ['resultForIdenticals' => $testMarker]
        );

        static::assertSame(
            $testMarker,
            $diffResult,
            'Rederer options: result for identicals should work.'
        );
    }

    /**
     * Test the AbstractRenderer::setOptions with an invalid result for identicals.
     *
     * @covers \Jfcherng\Diff\Renderer\AbstractRenderer::setOptions
     */
    public function testSetOptionsWithInvalidResultForIdenticals(): void
    {
        static::expectException(\InvalidArgumentException::class);

        $diffResult = DiffHelper::calculate(
            'we are the same',
            'we are the same',
            'Inline',
            [],
            ['resultForIdenticals' => 50]
        );
    }

    /**
     * Test HTML renderers should be able to render with JSON renderer's result.
     *
     * @covers \Jfcherng\Diff\Renderer\AbstractRenderer::renderArray
     */
    public function testHtmlRendererRenderWithResultFromJsonRenderer(): void
    {
        $htmlRenderer = RendererFactory::make('Inline');

        // test "outputTagAsString" is false
        $jsonResult = DiffHelper::calculate(
            'old marker',
            'new marker',
            'Json',
            [],
            ['outputTagAsString' => false]
        );
        $jsonArray = \json_decode($jsonResult, true);
        $inlineResult = $htmlRenderer->renderArray($jsonArray);

        static::assertStringContainsString(
            '><del>old</del> marker<',
            $inlineResult,
            "HTML renderers should be able to render with JSON result. ('outputTagAsString' => false)"
        );

        // test "outputTagAsString" is true
        $jsonResult = DiffHelper::calculate(
            'old marker',
            'new marker',
            'Json',
            [],
            ['outputTagAsString' => true]
        );
        $jsonArray = \json_decode($jsonResult, true);
        $inlineResult = $htmlRenderer->renderArray($jsonArray);

        static::assertStringContainsString(
            '><del>old</del> marker<',
            $inlineResult,
            "HTML renderers should be able to render with JSON result. ('outputTagAsString' => true)"
        );
    }

    /**
     * Test text renderers are not able to render with JSON renderer's result.
     *
     * @covers \Jfcherng\Diff\Renderer\AbstractRenderer::renderArray
     */
    public function testTextRendererRenderWithResultFromJsonRenderer(): void
    {
        static::expectException(UnsupportedFunctionException::class);

        $jsonResult = DiffHelper::calculate('old marker', 'new marker', 'Json');
        $jsonArray = \json_decode($jsonResult, true);

        $textRenderer = RendererFactory::make('Unified');
        $UnifiedResult = $textRenderer->renderArray($jsonArray);
    }
}
