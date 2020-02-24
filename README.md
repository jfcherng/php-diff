# php-diff

<a href="https://travis-ci.org/jfcherng/php-diff"><img alt="Travis (.org) branch" src="https://img.shields.io/travis/jfcherng/php-diff/v6?style=flat-square"></a>
<a href="https://app.codacy.com/project/jfcherng/php-diff/dashboard"><img alt="Codacy grade" src="https://img.shields.io/codacy/grade/3a7a07d2ed67434e8e8582ea4ec9867b/v6?style=flat-square"></a>
<a href="https://packagist.org/packages/jfcherng/php-diff"><img alt="Packagist" src="https://img.shields.io/packagist/dt/jfcherng/php-diff?style=flat-square"></a>
<a href="https://packagist.org/packages/jfcherng/php-diff"><img alt="Packagist Version" src="https://img.shields.io/packagist/v/jfcherng/php-diff?style=flat-square"></a>
<a href="https://github.com/jfcherng/php-diff/blob/v6/LICENSE"><img alt="Project license" src="https://img.shields.io/github/license/jfcherng/php-diff?style=flat-square"></a>
<a href="https://github.com/jfcherng/php-diff/stargazers"><img alt="GitHub stars" src="https://img.shields.io/github/stars/jfcherng/php-diff?style=flat-square&logo=github"></a>
<a href="https://www.paypal.me/jfcherng/5usd" title="Donate to this project using Paypal"><img src="https://img.shields.io/badge/paypal-donate-blue.svg?style=flat-square&logo=paypal" /></a>

A comprehensive library for generating diff between two strings.


## Introduction

Generated diff can be rendered in all of the standard formats including:

**Text** renderers:

- Context
- Json
- Unified

**HTML** renderers:

- Inline
- Side by Side

Note that for HTML rendered results, you have to add CSS for a better visualization.
You may modify one from `example/diff-table.css` or write your own from zero.

If you are okay with the default CSS, there is `\Jfcherng\Diff\DiffHelper::getStyleSheet()`
which can be used to get the content of the `example/diff-table.css`.


## Requirements

![php](https://img.shields.io/badge/php-%5E7.1.3-blue?style=flat-square)
![ext-iconv](https://img.shields.io/badge/ext-iconv-brightgreen?style=flat-square)


## Installation

This package is available on `Packagist` by the name of [jfcherng/php-diff](https://packagist.org/packages/jfcherng/php-diff).

```bash
composer require jfcherng/php-diff
```


## Example

See `demo.php` in the [example/](https://github.com/jfcherng/php-diff/blob/v6/example) and files under `tests/`.

```php
<?php

include __DIR__ . '/vendor/autoload.php';

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Factory\RendererFactory;

$oldFile = __DIR__ . '/example/old_file.txt';
$newFile = __DIR__ . '/example/new_file.txt';

$old = 'This is the old string.';
$new = 'And this is the new one.';

// renderer class name:
//     Text renderers: Context, Json, Unified
//     HTML renderers: Inline, SideBySide
$rendererName = 'Unified';

// the Diff class options
$differOptions = [
    // show how many neighbor lines
    'context' => 3,
    // ignore case difference
    'ignoreCase' => false,
    // ignore whitespace difference
    'ignoreWhitespace' => false,
];

// the renderer class options
$rendererOptions = [
    // how detailed the rendered HTML in-line diff is? (none, line, word, char)
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

// one-line simply compare two files
$result = DiffHelper::calculateFiles($oldFile, $newFile, $rendererName, $differOptions, $rendererOptions);
// one-line simply compare two strings
$result = DiffHelper::calculate($old, $new, $rendererName, $differOptions, $rendererOptions);
// or even shorter if you are happy with default options
$result = DiffHelper::calculate($old, $new, $rendererName);

// custom usage
$differ = new Differ(explode("\n", $old), explode("\n", $new), $differOptions);
$renderer = RendererFactory::make($rendererName, $rendererOptions); // or your own renderer object
$result = $renderer->render($differ);

// use the JSON result to render in HTML
$jsonResult = DiffHelper::calculate($old, $new, 'Json'); // may store the JSON result in your database
$htmlRenderer = RendererFactory::make('Inline', $rendererOptions);
$result = $htmlRenderer->renderArray(json_decode($jsonResult, true));
```


## Rendered Results


### HTML Diff In-line Detailed Rendering

<table>
  <tr>
    <th>None-level</th>
    <th>Line-level (Default)</th>
  </tr>
  <tr>
    <td><img src="https://raw.githubusercontent.com/jfcherng/php-diff/v6/example/images/inline-none-level-diff.png"></td>
    <td><img src="https://raw.githubusercontent.com/jfcherng/php-diff/v6/example/images/inline-line-level-diff.png"></td>
  </tr>
  <tr>
    <th>Word-level</th>
    <th>Char-level</th>
  </tr>
  <tr>
    <td><img src="https://raw.githubusercontent.com/jfcherng/php-diff/v6/example/images/inline-word-level-diff.png"></td>
    <td><img src="https://raw.githubusercontent.com/jfcherng/php-diff/v6/example/images/inline-char-level-diff.png"></td>
  </tr>
</table>


### Renderer: Inline

```php
<?php $rendererOptions = ['detailLevel' => 'line'];
```

![Inline](https://raw.githubusercontent.com/jfcherng/php-diff/v6/example/images/inline-renderer.png)


### Renderer: Side By Side

```php
<?php $rendererOptions = ['detailLevel' => 'line'];
```

![Side By Side](https://raw.githubusercontent.com/jfcherng/php-diff/v6/example/images/side-by-side-renderer.png)


### Renderer: Unified

```diff
@@ -1,3 +1,4 @@
-<p>Hello World!</p>
+<div>Hello World!</div>
 ~~~~~~~~~~~~~~~~~~~
+Let's add a new line here.
 X
@@ -7,6 +8,5 @@
 N
-Do you know in Chinese, "金槍魚罐頭" means tuna can.
+Do you know in Japanese, "魚の缶詰" means fish can.
 This is just a useless line.
 G
-// @todo Remember to delete this line
 Say hello to my neighbors.
```


### Renderer: Context

<details><summary>Click to expand</summary>

```diff
***************
*** 1,3 ****
! <p>Hello World!</p>
  ~~~~~~~~~~~~~~~~~~~
  X
--- 1,4 ----
! <div>Hello World!</div>
  ~~~~~~~~~~~~~~~~~~~
+ Let's add a new line here.
  X
***************
*** 7,12 ****
  N
! Do you know in Chinese, "金槍魚罐頭" means tuna can.
  This is just a useless line.
  G
- // @todo Remember to delete this line
  Say hello to my neighbors.
--- 8,12 ----
  N
! Do you know in Japanese, "魚の缶詰" means fish can.
  This is just a useless line.
  G
  Say hello to my neighbors.
```

</details>


### Renderer: JSON

<details><summary>Click to expand</summary>

```json
[
    [
        {
            "tag": 8,
            "old": {
                "offset": 0,
                "lines": [
                    "&lt;<del>p&gt;Hello World!&lt;/p</del>&gt;"
                ]
            },
            "new": {
                "offset": 0,
                "lines": [
                    "&lt;<ins>div&gt;Hello World!&lt;/div</ins>&gt;"
                ]
            }
        },
        {
            "tag": 1,
            "old": {
                "offset": 1,
                "lines": [
                    "~~~~~~~~~~~~~~~~~~~"
                ]
            },
            "new": {
                "offset": 1,
                "lines": [
                    "~~~~~~~~~~~~~~~~~~~"
                ]
            }
        },
        {
            "tag": 4,
            "old": {
                "offset": 2,
                "lines": []
            },
            "new": {
                "offset": 2,
                "lines": [
                    "Let's add a new line here."
                ]
            }
        },
        {
            "tag": 1,
            "old": {
                "offset": 2,
                "lines": [
                    "X"
                ]
            },
            "new": {
                "offset": 3,
                "lines": [
                    "X"
                ]
            }
        }
    ],
    [
        {
            "tag": 1,
            "old": {
                "offset": 6,
                "lines": [
                    "N"
                ]
            },
            "new": {
                "offset": 7,
                "lines": [
                    "N"
                ]
            }
        },
        {
            "tag": 8,
            "old": {
                "offset": 7,
                "lines": [
                    "Do you know in <del>Chinese, \"金槍魚罐頭\" means tuna</del> can."
                ]
            },
            "new": {
                "offset": 8,
                "lines": [
                    "Do you know in <ins>Japanese, \"魚の缶詰\" means fish</ins> can."
                ]
            }
        },
        {
            "tag": 1,
            "old": {
                "offset": 8,
                "lines": [
                    "This is just a useless line.",
                    "G"
                ]
            },
            "new": {
                "offset": 9,
                "lines": [
                    "This is just a useless line.",
                    "G"
                ]
            }
        },
        {
            "tag": 2,
            "old": {
                "offset": 10,
                "lines": [
                    "// @todo Remember to delete this line"
                ]
            },
            "new": {
                "offset": 11,
                "lines": []
            }
        },
        {
            "tag": 1,
            "old": {
                "offset": 11,
                "lines": [
                    "Say hello to my neighbors."
                ]
            },
            "new": {
                "offset": 11,
                "lines": [
                    "Say hello to my neighbors."
                ]
            }
        }
    ]
]
```

</details>


## Acknowledgment

This package is built on the top of [chrisboulton/php-diff](https://github.com/chrisboulton/php-diff) initially.
But the original repository looks like no longer maintained.
Here have been quite lots of rewrites and new features since then, hence I re-started this as a new package for better visibility.
