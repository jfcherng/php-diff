# php-diff [![Build Status](https://travis-ci.org/jfcherng/php-diff.svg?branch=master)](https://travis-ci.org/jfcherng/php-diff)

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

- Some bug fixes and performance rewrites.
- UTF-8-ready.
- Follow `PSR-1`, `PSR-2`, `PSR-4`.
- Use PHP 7.1 features and make it fully type-hinted.
- Add `Json` template.
- Add character-level diff for HTML templates.
- Add class `DiffHelper` for simple usage.
- Add multi-language support (English, Chinese, etc...) for templates.


# Installation

```
$ composer require jfcherng/php-diff --no-dev
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

$templateOptions = [
    // template language: eng, cht, chs, jpn, ...
    'language' => 'eng',
    // HTML template tab width
    'tabSize' => 4,
];

// one-line simple usage
$result = DiffHelper::calculate($old, $new, $templates, $diffOptions, $templateOptions);

// custom usage
$diff = new Diff($old, $new, $diffOptions);
$renderer = RendererFactory::make($template, $templateOptions);
$result = $diff->render($renderer);
```


# Rendered Results


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
