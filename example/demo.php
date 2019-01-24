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

            // sample string for comparison
            $old = "\$old = 'This is the old string.';";
            $new = "\$new = 'And this is the new one.';";

            // sample string for comparison
            $old_u8 = '內存不足！沒法在視頻聊天時播放視頻。';
            $new_u8 = '記憶體不足！沒辦法在視訊聊天時播放影片。';

            // include two sample files for comparison
            $old_file = \file_get_contents(__DIR__ . '/old_file.txt');
            $new_file = \file_get_contents(__DIR__ . '/new_file.txt');

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

        <h1>UTF-8 Ready</h1>
        <?php

            // demo the UTF-8 diff
            $result = DiffHelper::calculate(
                $old_u8,
                $new_u8,
                'Inline',
                $diffOptions,
                ['detailLevel' => 'char'] + $templateOptions
            );

            echo $result;

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
            $result = DiffHelper::calculate($old_file, $new_file, 'SideBySide', $diffOptions, $templateOptions);

            echo $result;

        ?>

        <h1>Inline Diff</h1>
        <?php

            // generate an inline diff
            $result = DiffHelper::calculate($old_file, $new_file, 'Inline', $diffOptions, $templateOptions);

            echo $result;

        ?>

        <h1>Unified Diff</h1>
        <pre><?php

            // generate a unified diff
            $result = DiffHelper::calculate($old_file, $new_file, 'Unified', $diffOptions, $templateOptions);

            echo \htmlspecialchars($result);

        ?></pre>

        <h1>Context Diff</h1>
        <pre><?php

            // generate a context diff
            $result = DiffHelper::calculate($old_file, $new_file, 'Context', $diffOptions, $templateOptions);

            echo \htmlspecialchars($result);

        ?></pre>
    </body>
</html>
