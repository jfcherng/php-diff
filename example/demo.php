<?php

include __DIR__ . '/../vendor/autoload.php';

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Factory\RendererFactory;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>jfcherng/php-diff - Examples</title>
        <style><?php echo DiffHelper::getStyleSheet(); ?></style>
    </head>
    <body>
        <?php

        // the two sample files for comparison
        $oldFile = __DIR__ . '/old_file.txt';
        $newFile = __DIR__ . '/new_file.txt';
        $oldString = \file_get_contents($oldFile);
        $newString = \file_get_contents($newFile);

        // options for Diff class
        $diffOptions = [
            // show how many neighbor lines
            // Differ::CONTEXT_ALL can be used to show the whole file
            'context' => 1,
            // ignore case difference
            'ignoreCase' => false,
            // ignore whitespace difference
            'ignoreWhitespace' => false,
        ];

        // options for renderer class
        $rendererOptions = [
            // how detailed the rendered HTML is? (none, line, word, char)
            'detailLevel' => 'line',
            // renderer language: eng, cht, chs, jpn, ...
            // or an array which has the same keys with a language file
            'language' => 'eng',
            // show line numbers in HTML renderers
            'lineNumbers' => true,
            // show a separator between different diff hunks in HTML renderers
            'separateBlock' => true,
            // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
            // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
            'spacesToNbsp' => false,
            // HTML renderer tab width (negative = do not convert into spaces)
            'tabSize' => 4,
            // this option is currently only for the Json renderer.
            // internally, ops (tags) are all int type but this is not good for human reading.
            // set this to "true" to convert them into string form before outputting.
            'outputTagAsString' => false,
            // change this value to a string as the returned diff if the two input strings are identical
            'resultForIdenticals' => null,
            // extra HTML classes added to the DOM of the diff container
            'wrapperClasses' => ['diff-wrapper'],
        ];

        ?>

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
        <pre><?php

        // generate a unified diff
        $unifiedResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'Unified',
            $diffOptions,
            $rendererOptions
        );

        echo \htmlspecialchars($unifiedResult);

        ?></pre>

        <h1>Context Diff</h1>
        <pre><?php

        // generate a context diff
        $contextResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'Context',
            $diffOptions,
            $rendererOptions
        );

        echo \htmlspecialchars($contextResult);

        ?></pre>

        <h1>JSON Diff</h1>
        <pre><?php

        // generate a JSON diff
        $jsonResult = DiffHelper::calculateFiles(
            $oldFile,
            $newFile,
            'Json',
            $diffOptions,
            ['outputTagAsString' => true] + $rendererOptions
        );

        $beautified = \json_encode(
            \json_decode($jsonResult, true),
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_PRETTY_PRINT
        );

        echo $beautified;

        ?></pre>

        <h1>HTML Diff from the Result of JSON Diff</h1>
        <pre><?php

        $jsonArray = \json_decode($jsonResult, true);

        $htmlRenderer = RendererFactory::make('Inline', $rendererOptions);
        $inlineResult = $htmlRenderer->renderArray($jsonArray);

        echo $inlineResult;

        ?></pre>
    </body>
</html>
