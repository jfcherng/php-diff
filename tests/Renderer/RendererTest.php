<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test\Renderer;

use Jfcherng\Diff\DiffHelper;
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
}
