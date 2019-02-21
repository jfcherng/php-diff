[![Build Status](https://travis-ci.org/jfcherng/php-diff.svg?branch=v4)](https://travis-ci.org/jfcherng/php-diff)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/3a7a07d2ed67434e8e8582ea4ec9867b)](https://app.codacy.com/app/jfcherng/php-diff?utm_source=github.com&utm_medium=referral&utm_content=jfcherng/php-diff&utm_campaign=Badge_Grade_Dashboard)

# php-diff

A comprehensive library for generating diff between two strings.


# Introduction

Generated diff can be rendered in all of the standard formats including:

- Unified (Text)
- Context (Text)
- Json (Text)
- Inline (HTML)
- Side by Side (HTML)


# Requirements

- PHP >= 7.1.3
- Extension: `iconv`


# Installation

```
$ composer require jfcherng/php-diff
```


# Example

See [example/demo.php](https://github.com/jfcherng/php-diff/blob/v4/example/demo.php) and files under `tests/`.

```php
<?php

include __DIR__ . '/vendor/autoload.php';

use Jfcherng\Diff\Diff;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Factory\RendererFactory;

$old = 'This is the old string.';
$new = 'And this is the new one.';

// template class name: Unified, Context, Json, Inline, SideBySide
$template = 'Unified';

// the Diff class options
$diffOptions = [
    // show how many neighbor lines
    'context' => 3,
    // ignore case difference
    'ignoreCase' => false,
    // ignore whitespace difference
    'ignoreWhitespace' => false,
];

// the template class options
$templateOptions = [
    // how detailed the rendered HTML in-line diff is? (none, line, word, char)
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
];

// one-line simple usage
$result = DiffHelper::calculate($old, $new, $template, $diffOptions, $templateOptions);
// or even shorter if you are happy with default options
$result = DiffHelper::calculate($old, $new, $template);

// custom usage
$diff = new Diff(explode("\n", $old), explode("\n", $new), $diffOptions);
$renderer = RendererFactory::make($template, $templateOptions); // or your own renderers
$result = $diff->render($renderer);
```


# Rendered Results


## HTML Diff In-line Detailed Rendering

<table>
  <tr>
    <td>None-level</td>
    <td>Line-level (Default)</td>
  </tr>
  <tr>
    <td><img src="https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/inline-none-level-diff.png"></td>
    <td><img src="https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/inline-line-level-diff.png"></td>
  </tr>
  <tr>
    <td>Word-level</td>
    <td>Char-level</td>
  </tr>
  <tr>
    <td><img src="https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/inline-word-level-diff.png"></td>
    <td><img src="https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/inline-char-level-diff.png"></td>
  </tr>
</table>


## Renderer: Inline

![Inline](https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/inline-renderer.png)


## Renderer: Side By Side

![Side By Side](https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/side-by-side-renderer.png)


## Renderer: Unified

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


## Renderer: Context

```
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


## Renderer: JSON

<details><summary>Click to expand</summary>

```javascript
[
    [
        {
            "tag": "rep",
            "base": {
                "offset": 0,
                "lines": [
                    "&lt;<del>p&gt;Hello World!&lt;/p</del>&gt;"
                ]
            },
            "changed": {
                "offset": 0,
                "lines": [
                    "&lt;<ins>div&gt;Hello World!&lt;/div</ins>&gt;"
                ]
            }
        },
        {
            "tag": "eq",
            "base": {
                "offset": 1,
                "lines": [
                    "~~~~~~~~~~~~~~~~~~~"
                ]
            },
            "changed": {
                "offset": 1,
                "lines": [
                    "~~~~~~~~~~~~~~~~~~~"
                ]
            }
        },
        {
            "tag": "ins",
            "base": {
                "offset": 2,
                "lines": []
            },
            "changed": {
                "offset": 2,
                "lines": [
                    "Let's add a new line here."
                ]
            }
        },
        {
            "tag": "eq",
            "base": {
                "offset": 2,
                "lines": [
                    "X"
                ]
            },
            "changed": {
                "offset": 3,
                "lines": [
                    "X"
                ]
            }
        }
    ],
    [
        {
            "tag": "eq",
            "base": {
                "offset": 6,
                "lines": [
                    "N"
                ]
            },
            "changed": {
                "offset": 7,
                "lines": [
                    "N"
                ]
            }
        },
        {
            "tag": "rep",
            "base": {
                "offset": 7,
                "lines": [
                    "Do you know in <del>Chinese, \"金槍魚罐頭\" means tuna</del> can."
                ]
            },
            "changed": {
                "offset": 8,
                "lines": [
                    "Do you know in <ins>Japanese, \"魚の缶詰\" means fish</ins> can."
                ]
            }
        },
        {
            "tag": "eq",
            "base": {
                "offset": 8,
                "lines": [
                    "This is just a useless line.",
                    "G"
                ]
            },
            "changed": {
                "offset": 9,
                "lines": [
                    "This is just a useless line.",
                    "G"
                ]
            }
        },
        {
            "tag": "del",
            "base": {
                "offset": 10,
                "lines": [
                    "// @todo Remember to delete this line"
                ]
            },
            "changed": {
                "offset": 11,
                "lines": []
            }
        },
        {
            "tag": "eq",
            "base": {
                "offset": 11,
                "lines": [
                    "Say hello to my neighbors."
                ]
            },
            "changed": {
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


# Acknowledgment

This package is built on the top of [chrisboulton/php-diff](https://github.com/chrisboulton/php-diff) initially.
But the original repository looks like no longer maintained.
Here have been quite lots of rewrites and new features since then, hence I re-started this as a new package for better visibility.


Supporters <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ATXYY9Y78EQ3Y" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" /></a>
==========

Thank you guys for sending me some cups of coffee.
