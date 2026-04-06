<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test\Options;

use Jfcherng\Diff\Options\RendererOptions;
use Jfcherng\Diff\Renderer\RendererConstant;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RendererOptionsTest extends TestCase
{
    /**
     * @covers \Jfcherng\Diff\Options\RendererOptions::__construct
     */
    public function testDefaultValues(): void
    {
        $opts = new RendererOptions();

        self::assertSame('line', $opts->detailLevel);
        self::assertSame('eng', $opts->language);
        self::assertTrue($opts->lineNumbers);
        self::assertTrue($opts->separateBlock);
        self::assertTrue($opts->showHeader);
        self::assertFalse($opts->spaceToHtmlTag);
        self::assertFalse($opts->spacesToNbsp);
        self::assertSame(4, $opts->tabSize);
        self::assertSame(0.8, $opts->mergeThreshold);
        self::assertSame(RendererConstant::CLI_COLOR_AUTO, $opts->cliColorization);
        self::assertFalse($opts->outputTagAsString);
        self::assertSame(\JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE, $opts->jsonEncodeFlags);
        self::assertSame(['-', ' '], $opts->wordGlues);
        self::assertNull($opts->resultForIdenticals);
        self::assertSame(['diff-wrapper'], $opts->wrapperClasses);
    }

    /**
     * @covers \Jfcherng\Diff\Options\RendererOptions::fromArray
     */
    public function testFromArrayOverridesDefaults(): void
    {
        $opts = RendererOptions::fromArray(['lineNumbers' => false, 'tabSize' => 2]);

        self::assertFalse($opts->lineNumbers);
        self::assertSame(2, $opts->tabSize);
        self::assertTrue($opts->showHeader); // default preserved
    }

    /**
     * @covers \Jfcherng\Diff\Options\RendererOptions::__construct
     */
    public function testValueEquality(): void
    {
        $a = new RendererOptions(tabSize: 2);
        $b = new RendererOptions(tabSize: 2);

        self::assertSame($a->tabSize, $b->tabSize);   // same values → equal
        self::assertNotSame($a, $b);                  // different instances
    }
}
