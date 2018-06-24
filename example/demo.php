<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>jfcherng/php-diff - Examples</title>
        <link href="./styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php

            // include the diff class
            include __DIR__ . '/../vendor/autoload.php';

            use Jfcherng\Diff\DiffHelper;

            // include two sample files for comparison
            $a = file_get_contents(__DIR__ . '/a.txt');
            $b = file_get_contents(__DIR__ . '/b.txt');

            // options for generating the diff
            $diffOptions = [
                // 'ignoreWhitespace' => true,
                // 'ignoreCase' => true,
            ];

        ?>

        <h1>Side by Side Diff</h1>
        <?php

            // generate a side by side diff
            $result = DiffHelper::calculate($a, $b, 'SideBySide', $diffOptions);
            echo $result;

        ?>

        <h1>Inline Diff</h1>
        <?php

            // generate an inline diff
            $result = DiffHelper::calculate($a, $b, 'Inline', $diffOptions);
            echo $result;

        ?>

        <h1>Unified Diff</h1>
        <pre><?php

            // generate a unified diff
            $result = DiffHelper::calculate($a, $b, 'Unified', $diffOptions);
            echo htmlspecialchars($result);

        ?>
        </pre>

        <h1>Context Diff</h1>
        <pre><?php

            // generate a context diff
            $result = DiffHelper::calculate($a, $b, 'Context', $diffOptions);
            echo htmlspecialchars($result);

        ?>
        </pre>
    </body>
</html>
