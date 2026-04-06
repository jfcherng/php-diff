<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test\Options;

use Jfcherng\Diff\Options\DifferOptions;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DifferOptionsTest extends TestCase
{
    /**
     * @covers \Jfcherng\Diff\Options\DifferOptions::__construct
     */
    public function testDefaultValues(): void
    {
        $opts = new DifferOptions();

        self::assertSame(3, $opts->context);
        self::assertFalse($opts->ignoreCase);
        self::assertFalse($opts->ignoreLineEnding);
        self::assertFalse($opts->ignoreWhitespace);
        self::assertSame(2000, $opts->lengthLimit);
        self::assertFalse($opts->fullContextIfIdentical);
    }

    /**
     * @covers \Jfcherng\Diff\Options\DifferOptions::fromArray
     */
    public function testFromArrayOverridesDefaults(): void
    {
        $opts = DifferOptions::fromArray(['context' => 5, 'ignoreCase' => true]);

        self::assertSame(5, $opts->context);
        self::assertTrue($opts->ignoreCase);
        self::assertFalse($opts->ignoreWhitespace); // default preserved
    }

    /**
     * @covers \Jfcherng\Diff\Options\DifferOptions::toArray
     */
    public function testToArrayRoundTrip(): void
    {
        $arr = ['context' => 1, 'ignoreCase' => true, 'ignoreLineEnding' => false,
            'ignoreWhitespace' => false, 'lengthLimit' => 500, 'fullContextIfIdentical' => true];

        self::assertSame($arr, DifferOptions::fromArray($arr)->toArray());
    }

    /**
     * @covers \Jfcherng\Diff\Options\DifferOptions::__construct
     */
    public function testValueEquality(): void
    {
        $a = new DifferOptions(context: 5);
        $b = new DifferOptions(context: 5);

        self::assertEquals($a, $b);    // same values → equal
        self::assertNotSame($a, $b);   // different instances
    }
}
