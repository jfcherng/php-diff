<?php

include __DIR__ . '/demo_base.php';

use Jfcherng\Diff\DiffHelper;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\StreamOutput;

$output = new StreamOutput(
    \fopen('php://stdout', 'w'),
    StreamOutput::VERBOSITY_NORMAL,
    null,
    new OutputFormatter(
        false,
        [
            'section' => new OutputFormatterStyle('black', 'cyan', []),
        ]
    )
);

$output->write("<section>Unified Diff\n============</>\n\n");

// generate a unified diff
$unifiedResult = DiffHelper::calculate(
    $oldString,
    $newString,
    'Unified',
    $diffOptions,
    $rendererOptions
);

echo $unifiedResult . "\n\n\n\n";

$output->write("<section>Context Diff\n============</>\n\n");

// generate a context diff
$contextResult = DiffHelper::calculate(
    $oldString,
    $newString,
    'Context',
    $diffOptions,
    $rendererOptions
);

echo $contextResult . "\n\n\n\n";
