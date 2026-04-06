## Upgrading to v7

### PHP version requirement raised to 8.3

The minimum supported PHP version is now **8.3**.

### [BREAKING CHANGE] `DifferOptions` value object

Differ options are now represented by `Jfcherng\Diff\Options\DifferOptions` instead of a plain associative array.
This change makes it easier to be statically analyzed.

**Before (v6):**

```php
<?php

$differ = new Differ($old, $new, [
    'context' => 3,
    'ignoreCase' => false,
    'ignoreLineEnding' => false,
    'ignoreWhitespace' => false,
    'lengthLimit' => 2000,
    'fullContextIfIdentical' => false,
]);
```

**After (v7):**

```php
<?php

use Jfcherng\Diff\Options\DifferOptions;

$differ = new Differ($old, $new, new DifferOptions(
    context: 3,
    ignoreCase: false,
    ignoreLineEnding: false,
    ignoreWhitespace: false,
    lengthLimit: 2000,
    fullContextIfIdentical: false,
));
```

`Differ::__construct()`, `DiffHelper::calculate()`, and
`DiffHelper::calculateFiles()` still accept a plain array for backward
compatibility — it is converted to `DifferOptions` internally via
`DifferOptions::fromArray()`. Passing a typed object is now preferred.

The public property `Differ::$options` is now of type `DifferOptions`
instead of `array`.

### [BREAKING CHANGE] `RendererOptions` value object

It's similar to `DifferOptions`.

Renderer options are now represented by `Jfcherng\Diff\Options\RendererOptions` instead of a plain associative array.

**Before (v6):**

```php
<?php

$renderer = RendererFactory::make('Inline', [
    'detailLevel' => 'line',
    'language' => 'eng',
    'lineNumbers' => true,
    'separateBlock' => true,
    'showHeader' => true,
    'spacesToNbsp' => false,
    'tabSize' => 4,
    'mergeThreshold' => 0.8,
    'cliColorization' => RendererConstant::CLI_COLOR_AUTO,
    'outputTagAsString' => false,
    'jsonEncodeFlags' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
    'wordGlues' => ['-', ' '],
    'resultForIdenticals' => null,
    'wrapperClasses' => ['diff-wrapper'],
]);
```

**After (v7):**

```php
<?php

use Jfcherng\Diff\Options\RendererOptions;

$renderer = RendererFactory::make('Inline', new RendererOptions(
    detailLevel: 'line',
    language: 'eng',
    lineNumbers: true,
    separateBlock: true,
    showHeader: true,
    spaceToHtmlTag: false,
    spacesToNbsp: false,
    tabSize: 4,
    mergeThreshold: 0.8,
    cliColorization: RendererConstant::CLI_COLOR_AUTO,
    outputTagAsString: false,
    jsonEncodeFlags: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
    wordGlues: ['-', ' '],
    resultForIdenticals: null,
    wrapperClasses: ['diff-wrapper'],
));
```

`RendererFactory::make()` and `AbstractRenderer::setOptions()` still accept
a plain array for backward compatibility — it is converted via
`RendererOptions::fromArray()` internally. Passing a typed object is preferred.

### `jfcherng/php-sequence-matcher` bumped to v5

The `SequenceMatcher::setOptions()` method now requires a `Jfcherng\Diff\SequenceMatcherOptions` object instead of an array.
This only affects code that instantiates or configures `SequenceMatcher` directly.
Users interacting solely through `Differ` or `DiffHelper` are not affected.

`DifferOptions` exposes a `toSequenceMatcherOptions()` helper to convert between the two types.
