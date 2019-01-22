[![Build Status](https://travis-ci.org/jfcherng/php-diff.svg?branch=master)](https://travis-ci.org/jfcherng/php-diff)
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

The logic behind the core of the diff engine (i.e., the sequence matcher) is primarily based on the [Python difflib package](https://docs.python.org/3/library/difflib.html).
The reason for doing so is primarily because of its high degree of accuracy.


# Changes After Forking

- Various bug fixes and performance rewrites.
- Install with Composer.
- UTF-8-ready.
- Follow `PSR-1`, `PSR-2`, `PSR-4`.
- Utilize PHP 7.1 features and make it type-hinted.
- Add `Json` template.
- Add char-level and word-level diff for HTML templates.
- Add classes `DiffHelper` and `RendererFactory` for simple usage.
- Add multi-language support (English, Chinese, etc...) for templates.


# Requirements

- PHP >= 7.1.3
- Extension: `iconv`


# Installation

```
$ composer require jfcherng/php-diff
```


# Example

See [example/demo.php](https://github.com/jfcherng/php-diff/blob/master/example/demo.php) and files under `tests/`.

```php
<?php

include __DIR__ . '/vendor/autoload.php';

use Jfcherng\Diff\Diff;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Utility\RendererFactory;

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
    // how detailed the redered HTML is? (line, word, char)
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

// one-line simple usage
$result = DiffHelper::calculate($old, $new, $template, $diffOptions, $templateOptions);
// or even shorter if you are happy with default options
$result = DiffHelper::calculate($old, $new, $template);

// custom usage
$diff = new Diff(explode("\n", $old), explode("\n", $new), $diffOptions);
$renderer = RendererFactory::make($template, $templateOptions);
$result = $diff->render($renderer);
```


# Rendered Results


## Word-level Diff

![Word-level Diff](https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/word-level-diff.png)


## Char-level Diff

![Char-level Diff](https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/char-level-diff.png)


## Inline

![Inline](https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/inline.png)


## Side By Side

![Side By Side](https://raw.githubusercontent.com/jfcherng/php-diff/gh-pages/images/side-by-side.png)


## Unified

```diff
@@ -1,13 +1,14 @@
 <html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
-       <title>Hello World!</title>
+       <title>Goodbye Cruel World!</title>
    </head>
    <body>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

-       <h2>A heading we'll be removing</h2>

        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
+
+       <p>Just a small amount of new text...</p>
    </body>
 </html>
```


## Context

```
***************
*** 1,13 ****
  <html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
!       <title>Hello World!</title>
    </head>
    <body>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

-       <h2>A heading we'll be removing</h2>

        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
    </body>
  </html>
--- 1,14 ----
  <html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
!       <title>Goodbye Cruel World!</title>
    </head>
    <body>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>


        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
+
+       <p>Just a small amount of new text...</p>
    </body>
  </html>
```


Supporters <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ATXYY9Y78EQ3Y" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" /></a>
==========

Thank you guys for sending me some cups of coffee.
