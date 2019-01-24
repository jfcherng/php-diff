<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>jfcherng/php-diff - Examples</title>
        <link href="./styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php

            include __DIR__ . '/../vendor/autoload.php';

            use Jfcherng\Diff\DiffHelper;

            // include two sample files for comparison
            $a = \file_get_contents(__DIR__ . '/a.txt');
            $b = \file_get_contents(__DIR__ . '/b.txt');

            // sample string for comparison
            $old = "\$old = 'This is the old string.';";
            $new = "\$new = 'And this is the new one.';";

            // options for Diff class
            $diffOptions = [
                // show how many neighbor lines
                'context' => 3,
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
                // show "..." row in HTML templates
                'separateBlock' => true,
                // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
                // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
                'spacesToNbsp' => false,
                // HTML template tab width
                'tabSize' => 4,
            ];

        ?>

        <h1>None-level Diff</h1>
        <?php

            // demo the no-inline-detail diff
            $result = DiffHelper::calculate(
                $old,
                $new,
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
                $old,
                $new,
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
                $old,
                $new,
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
                $old,
                $new,
                'Inline',
                $diffOptions,
                ['detailLevel' => 'char'] + $templateOptions
            );

            echo $result;

        ?>

        <h1>Side by Side Diff</h1>
        <?php

            // generate a side by side diff
            $result = DiffHelper::calculate($a, $b, 'SideBySide', $diffOptions, $templateOptions);

            echo $result;

        ?>

        <h1>Inline Diff</h1>
        <?php

            // generate an inline diff
            $result = DiffHelper::calculate($a, $b, 'Inline', $diffOptions, $templateOptions);

            echo $result;

        ?>

        <h1>Unified Diff</h1>
        <pre><?php

            // generate a unified diff
            $result = DiffHelper::calculate($a, $b, 'Unified', $diffOptions, $templateOptions);

            echo \htmlspecialchars($result);

        ?></pre>

        <h1>Context Diff</h1>
        <pre><?php

            // generate a context diff
            $result = DiffHelper::calculate($a, $b, 'Context', $diffOptions, $templateOptions);

            echo \htmlspecialchars($result);

        ?></pre>
    </body>
</html>
