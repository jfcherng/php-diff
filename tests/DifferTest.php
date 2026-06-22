<?php

// @php-cs-fixer-ignore heredoc_indentation

declare(strict_types=1);

namespace Jfcherng\Diff\Test;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\SequenceMatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * @internal
 */
final class DifferTest extends TestCase
{
    /**
     * Test the Differ::getGroupedOpcodes.
     *
     * @covers \Jfcherng\Diff\Differ::getGroupedOpcodes
     */
    #[DataProvider('provideGetGroupedOpcodesCases')]
    public function testGetGroupedOpcodes(string $old, string $new, array $expected): void
    {
        $old = explode("\n", $old);
        $new = explode("\n", $new);

        self::assertSame(
            $expected,
            (new Differ($old, $new))->getGroupedOpcodes(),
        );
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
EOT,
                <<<'EOT'
apples
kiwis
carrots
grapefruits
EOT,
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
     * Test that numeric strings are compared as strings, not numbers.
     *
     * @covers \Jfcherng\Diff\Differ::getOldNewAreSame
     *
     * @see https://github.com/jfcherng/php-diff/issues/94
     */
    #[DataProvider('provideNumericStringComparisonCases')]
    public function testNumericStringComparison(string $old, string $new, bool $expectedAreSame): void
    {
        $differ = new Differ(explode("\n", $old), explode("\n", $new));

        self::assertSame($expectedAreSame, $differ->getOldNewAreSame());
    }

    /**
     * Data provider for testNumericStringComparison.
     */
    public static function provideNumericStringComparisonCases(): iterable
    {
        return [
            'zero vs multiple zeros' => ['0', '00000', false],
            'identical zeros' => ['0', '0', true],
            'numeric strings with different values' => ['1', '01', false],
            'leading zeros' => ['007', '7', false],
        ];
    }
}
