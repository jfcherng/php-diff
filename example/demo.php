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

            // options for Diff class
            $diffOptions = [
                // enable character-level diff
                'charLevelDiff' => false,
                // show how many neighbor lines
                'context' => 3,
                // ignore case difference
                'ignoreCase' => false,
                // ignore whitespace difference
                'ignoreWhitespace' => false,
                // show "..." row in HTML templates
                'separateBlock' => true,
            ];

            // options for template class
            $templateOptions = [
                // template language: eng, cht, chs, jpn, ...
                // or an array which has the same keys with a language file
                'language' => 'eng',
                // HTML template tab width
                'tabSize' => 4,
                // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
                // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
                'spacesToNbsp' => false,
            ];

        ?>

        <h1>Character-level Diff</h1>
        <?php

            $old = "\$old = 'This is the old string.';";
            $new = "\$new = 'And this is the new one.';";

            // demo the character-level diff
            $result = DiffHelper::calculate(
                $old,
                $new,
                'Inline',
                ['charLevelDiff' => true] + $diffOptions,
                $templateOptions
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
