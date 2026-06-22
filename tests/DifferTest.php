<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Test;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\SequenceMatcher;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * @internal
 */
final class DifferTest extends TestCase
{
    /**
     * Test that numeric strings that are equal numerically but not identical
     * (e.g. "00" vs "0") are correctly detected as different.
     *
     * @covers \Jfcherng\Diff\Differ::getOldNewComparison
     */
    public function testNumericStringComparisonIsStrict(): void
    {
        $differ = new Differ(['00'], ['0']);

        self::assertNotSame(0, $differ->getOldNewComparison());
    }

    /**
     * Data provider for Differ::getGroupedOpcodes.
     *
     * @return array the data provider
     */
    public static function provideGetGroupedOpcodesCases(): iterable
    {
        return [
            [
                <<<'EOT'
apples
oranges
kiwis
carrots
EOT
                ,
                <<<'EOT'
apples
kiwis
carrots
grapefruits
EOT
                ,
                [
                    [
                        [SequenceMatcher::OP_EQ, 0, 1, 0, 1],
                        [SequenceMatcher::OP_DEL, 1, 2, 1, 1],
                        [SequenceMatcher::OP_EQ, 2, 4, 1, 3],
                        [SequenceMatcher::OP_INS, 4, 4, 3, 4],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test the Differ::getGroupedOpcodes.
     *
     * @covers       \Jfcherng\Diff\Differ::getGroupedOpcodes
     *
     * @dataProvider provideGetGroupedOpcodesCases
     *
     * @param string $old      the old
     * @param string $new      the new
     * @param array  $expected the expected
     */
    public function testGetGroupedOpcodes(string $old, string $new, array $expected): void
    {
        $old = explode("\n", $old);
        $new = explode("\n", $new);

        self::assertSame(
            $expected,
            (new Differ($old, $new))->getGroupedOpcodes(),
        );
    }
}
