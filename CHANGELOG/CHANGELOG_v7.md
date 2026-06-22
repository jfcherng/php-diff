## v7.0.1

- Fix comparing numeric strings (e.g. `'0'` vs `'00000'`) producing an empty diff ([#94](https://github.com/jfcherng/php-diff/issues/94)).

## v7.0.0

There is no new features in this major release.
The main focus is to modernize the codebase and improve type safety.

- The minimum supported PHP version is now **8.3**.
- Introduce `DifferOptions` value object.
- Introduce `RendererOptions` value object.
