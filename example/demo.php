<?php

include __DIR__ . '/demo_base.php';

use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Factory\RendererFactory;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>jfcherng/php-diff - Examples</title>

        <!-- Prism -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.19.0/themes/prism-okaidia.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.19.0/plugins/line-numbers/prism-line-numbers.min.css" />

        <style type="text/css">
            html {
                font-size: 13px;
            }
            .token.coord {
                color: #6cf;
            }
            .token.diff.bold {
                color: #fb0;
                font-weight: normal;
            }

            <?php echo DiffHelper::getStyleSheet(); ?>
        </style>
    </head>
    <body>
        <h1>None-level Diff</h1>
        <?php

        // demo the no-inline-detail diff
        $inlineResult = DiffHelper::calculate(
            $oldString,
            $newString,
            'Inline',
            $diffOptions,
            ['detailLevel' => 'none'] + $rendererOptions
        );

        echo $inlineResult;

        ?>

        <h1>Line-level Diff (Default)</h1>
        <?php

        // demo the word-level diff
        $inlineResult = DiffHelper::calculate(
            $oldString,
            $newString,
            'Inline',
            $diffOptions,
            ['detailLevel' => 'line'] + $rendererOptions
        );

        echo $inlineResult;

        ?>

        <h1>Word-level Diff</h1>
        <?php

        // demo the word-level diff
        $inlineResult = DiffHelper::calculate(
            $oldString,
            $newString,
            'Inline',
            $diffOptions,
            ['detailLevel' => 'word'] + $rendererOptions
        );

        echo $inlineResult;

        ?>

        <h1>Character-level Diff</h1>
        <?php

        // demo the character-level diff
        $inlineResult = DiffHelper::calculate(
            $oldString,
            $newString,
            'Inline',
            $diffOptions,
            ['detailLevel' => 'char'] + $rendererOptions
        );

        echo $inlineResult;

        ?>

        <h1>Side by Side Diff</h1>
        <?php

        // generate a side by side diff
        $sideBySideResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'SideBySide',
            $diffOptions,
            $rendererOptions
        );

        echo $sideBySideResult;

        ?>

        <h1>Inline Diff</h1>
        <?php

        // generate an inline diff
        $inlineResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'Inline',
            $diffOptions,
            $rendererOptions
        );

        echo $inlineResult;

        ?>

        <h1>Combined Diff</h1>
        <?php

        // generate a combined diff
        $sideBySideResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'Combined',
            $diffOptions,
            $rendererOptions
        );

        echo $sideBySideResult;

        ?>

        <h1>Unified Diff</h1>
        <pre><code class="language-diff line-numbers"><?php

        // generate a unified diff
        $unifiedResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'Unified',
            $diffOptions,
            $rendererOptions
        );

        echo \htmlspecialchars($unifiedResult);

        ?></code></pre>

        <h1>Context Diff</h1>
        <pre><code class="language-diff line-numbers"><?php

        // generate a context diff
        $contextResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'Context',
            $diffOptions,
            $rendererOptions
        );

        echo \htmlspecialchars($contextResult);

        ?></code></pre>

        <h1>JSON Diff</h1>
        <pre><code class="language-json line-numbers"><?php

        // generate a JSON diff
        $jsonResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'Json',
            $diffOptions,
            [
                'outputTagAsString' => true,
                'jsonEncodeFlags' => (
                    \JSON_PRETTY_PRINT |
                    \JSON_UNESCAPED_SLASHES |
                    \JSON_UNESCAPED_UNICODE
                ),
            ] + $rendererOptions
        );

        echo $jsonResult;

        ?></code></pre>

        <h1>HTML Diff from the Result of JSON Diff</h1>
        <?php

        $jsonArray = \json_decode($jsonResult, true);

        $htmlRenderer = RendererFactory::make('Inline', $rendererOptions);
        $inlineResult = $htmlRenderer->renderArray($jsonArray);

        echo $inlineResult;

        ?>

        <!-- Prism -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.19.0/prism.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.19.0/components/prism-diff.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.19.0/components/prism-json.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.19.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
    </body>
</html>
