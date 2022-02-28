<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test;

use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Renderer\RendererConstant;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @coversNothing
 *
 * @internal
 */
final class DiffHelperTest extends TestCase
{
    /**
     * Test renderer output.
     *
     * @covers \Jfcherng\Diff\DiffHelper::calculate
     * @covers \Jfcherng\Diff\Renderer\Text\Context
     * @covers \Jfcherng\Diff\Renderer\Text\Unified
     *
     * @dataProvider rendererOutputDataProvider
     *
     * @param string        $rendererName The renderer name
     * @param int           $idx          The index
     * @param SplFileInfo[] $testFiles    The test files
     */
    public function testRendererOutput(string $rendererName, int $idx, array $testFiles): void
    {
        if (!isset($testFiles['old'], $testFiles['new'], $testFiles['result'])) {
            static::markTestSkipped("Renderer output test '{$rendererName}' #{$idx} is imcomplete.");
        }

        $result = DiffHelper::calculate(
            $testFiles['old']->getContents(),
            $testFiles['new']->getContents(),
            $rendererName,
            [],
            ['cliColorization' => RendererConstant::CLI_COLOR_DISABLE],
        );

        static::assertSame(
            $testFiles['result']->getContents(),
            $result,
            "Renderer output test '{$rendererName}' #{$idx} failed...",
        );
    }

    /**
     * Test the DiffHelper::getStyleSheet.
     *
     * @covers \Jfcherng\Diff\DiffHelper::getStyleSheet
     */
    public function testGetStyleSheet(): void
    {
        static::assertIsString(DiffHelper::getStyleSheet());
    }

    /**
     * Data provider for self::testRendererOutput.
     */
    public function rendererOutputDataProvider(): array
    {
        $rendererNames = DiffHelper::getAvailableRenderers();

        $data = [];

        foreach ($rendererNames as $rendererName) {
            $tests = $this->findRendererOutputTestFiles($rendererName);

            foreach ($tests as $idx => $files) {
                $data[] = [$rendererName, $idx, $files];
            }
        }

        return $data;
    }

    /**
     * Find renderer output test files.
     *
     * The structure is like [
     *     1 => ['old' => SplFileInfo, 'new' => SplFileInfo, 'result' => SplFileInfo],
     *     ...
     * ]
     *
     * @param string $rendererName The renderer name
     */
    protected function findRendererOutputTestFiles(string $rendererName): array
    {
        $rendererNameRegex = preg_quote($rendererName, '/');
        $fileNameRegex = "/{$rendererNameRegex}-(?P<idx>[0-9]+)-(?P<name>[^.\-]+)\.txt$/u";

        $finder = (new Finder())
            ->files()
            ->name($fileNameRegex)
            ->in(__DIR__ . '/data/renderer_outputs')
        ;

        $ret = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            preg_match($fileNameRegex, $file->getFilename(), $matches);
            $idx = (int) $matches['idx'];
            $name = $matches['name'];

            $ret[$idx] ??= [];
            $ret[$idx][$name] = $file;
        }

        return $ret;
    }
}
