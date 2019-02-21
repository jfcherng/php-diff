<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>jfcherng/php-diff - Examples</title>
        <link href="./diff-table.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php

            include __DIR__ . '/../vendor/autoload.php';

            use Jfcherng\Diff\DiffHelper;

            // include two sample files for comparison
            $old_file = \file_get_contents(__DIR__ . '/old_file.txt');
            $new_file = \file_get_contents(__DIR__ . '/new_file.txt');

            // options for Diff class
            $diffOptions = [
                // show how many neighbor lines
                'context' => 1,
                // ignore case difference
                'ignoreCase' => false,
                // ignore whitespace difference
                'ignoreWhitespace' => false,
            ];

            // options for template class
            $templateOptions = [
                // how detailed the rendered HTML is? (line, word, char)
                'detailLevel' => 'line',
                // template language: eng, cht, chs, jpn, ...
                // or an array which has the same keys with a language file
                'language' => 'eng',
                // show a separator between different diff hunks in HTML templates
                'separateBlock' => true,
                // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
                // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
                'spacesToNbsp' => false,
                // HTML template tab width (negative = do not convert into spaces)
                'tabSize' => 4,
                // internally, ops (tags) are all int type but this is not good for human reading.
                // set this to "true" to convert them into string form before outputting.
                'outputTagAsString' => false,
            ];

        ?>

        <h1>None-level Diff</h1>
        <?php

            // demo the no-inline-detail diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'Inline',
                $diffOptions,
                ['detailLevel' => 'none'] + $templateOptions
            );

            echo $result;

        ?>

        <h1>Line-level Diff (Default)</h1>
        <?php

            // demo the word-level diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'Inline',
                $diffOptions,
                ['detailLevel' => 'line'] + $templateOptions
            );

            echo $result;

        ?>

        <h1>Word-level Diff</h1>
        <?php

            // demo the word-level diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'Inline',
                $diffOptions,
                ['detailLevel' => 'word'] + $templateOptions
            );

            echo $result;

        ?>

        <h1>Character-level Diff</h1>
        <?php

            // demo the character-level diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'Inline',
                $diffOptions,
                ['detailLevel' => 'char'] + $templateOptions
            );

            echo $result;

        ?>

        <h1>Side by Side Diff</h1>
        <?php

            // generate a side by side diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'SideBySide',
                $diffOptions,
                $templateOptions
            );

            echo $result;

        ?>

        <h1>Inline Diff</h1>
        <?php

            // generate an inline diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'Inline',
                $diffOptions,
                $templateOptions
            );

            echo $result;

        ?>

        <h1>Unified Diff</h1>
        <pre><?php

            // generate a unified diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'Unified',
                $diffOptions,
                $templateOptions
            );

            echo \htmlspecialchars($result);

        ?></pre>

        <h1>Context Diff</h1>
        <pre><?php

            // generate a context diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'Context',
                $diffOptions,
                $templateOptions
            );

            echo \htmlspecialchars($result);

        ?></pre>

        <h1>JSON Diff</h1>
        <pre><?php

            // generate a JSON diff
            $result = DiffHelper::calculate(
                $old_file,
                $new_file,
                'Json',
                $diffOptions,
                ['outputTagAsString' => true] + $templateOptions
            );

            $beautified = \json_encode(
                \json_decode($result, true),
                \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_PRETTY_PRINT
            );

            echo $beautified;

        ?></pre>
    </body>
</html>
