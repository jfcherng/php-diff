<?php

include __DIR__ . '/demo_base.php';

use Jfcherng\Diff\DiffHelper;
use Jfcherng\Utility\CliColor;

$colorStyles = [
    'section' => ['f_black', 'b_cyan'],
];

echo CliColor::color("Unified Diff\n============", $colorStyles['section']) . "\n\n";

// generate a unified diff
$unifiedResult = DiffHelper::calculate(
    $oldString,
    $newString,
    'Unified',
    $diffOptions,
    $rendererOptions
);

echo $unifiedResult . "\n\n\n\n";

echo CliColor::color("Context Diff\n============", $colorStyles['section']) . "\n\n";

// generate a context diff
$contextResult = DiffHelper::calculate(
    $oldString,
    $newString,
    'Context',
    $diffOptions,
    $rendererOptions
);

echo $contextResult . "\n\n\n\n";
