# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

`php-diff` is a PHP library for generating diffs between strings in multiple output formats (unified, side-by-side HTML, context, JSON, etc.). Minimum PHP 8.4.

## Commands

```bash
composer run-script test      # phpunit --verbose
composer run-script analyze   # phan --color && phpcs --colors -n
composer run-script fix       # php-cs-fixer fix --verbose (auto-format)
composer run-script server    # dev server at localhost:12388
```

## Code Style

- PSR-12, 4-space indentation, LF line endings
- PHP-CS-Fixer 3 with `.php-cs-fixer.dist.php` config (risky mode enabled)
- Short array syntax (`[]`), trailing commas in multiline arrays/args
- Run `composer run-script fix` to auto-format before committing

## Testing

- PHPUnit 13; all test methods require `@covers` annotations
- `tests/data/` is excluded from linting — do not apply phpcs or php-cs-fixer there
- Trailing whitespace in test data is **intentional** (tests whitespace handling) — never strip it
- `no_trailing_whitespace_in_string` rule is disabled in php-cs-fixer for this reason

## Git Workflow

- Active branch: `v7` — branch from it using `feat/*` or `fix/*` naming
- Run `composer run-script analyze && composer run-script test` before marking work done

## Approach

- Think before acting. Read existing files before writing code.
- Be concise in output but thorough in reasoning.
- Prefer editing over rewriting whole files.
- Do not re-read files you have already read unless the file may have changed.
- Test your code before declaring done.
- No sycophantic openers or closing fluff.
- Keep solutions simple and direct.
- User instructions always override this file.
