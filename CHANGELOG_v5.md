
## VERSION 5  AFTERNOON

 * Version **5.1** - helper methods
   * 2019-02-27 01:47  **5.1.1**  fix typo
      * 868c542 Fix DiffHelp::getTemplatesInfo() typos
      * 80cd8e7 Revise phpdoc
      * 0a202c8 Add (private) Diff::finalize() to help maintain cache vadility
   * 2019-02-26 14:57  **5.1.0**  initial release
      * 00ae199 Fix .rmt.yml
      * 7a700fa Release of new version 4.2.0
      * 770636a php-cs-fixer
      * 0304537 Add INFO['type'] for renderers
      * a9bee51 Rewrite DiffHelper::getTemplatesInfo() and DiffHelper::getStyleSheet()
      * 9f9809e Add DiffHelper::getProjectDirectory()
      * 4d739c4 Add DiffHelper::getStyleSheet()
      * ec11495 require-dev at least phpunit/phpunit 7.5
      * 2553620 Add DiffHelper::calculateFiles()
      * a4f07c0 No need to clear cached value if $old/$new does not change
      * ff019a9 Code tidy
      * 02b30c8 Update deps
      * 742c096 Update UPGRADING_v5.md
      * 0ffd644 Code tidy
      * b619f91 Update readme

 * Version **5.0** - AFTERNOON
   * 2019-02-22 02:55  **5.0.2**  fix Json renderer with empty changes
      * 6ca5b97 Fix Json renderer with empty changes
      * e512f2d Update CHANGELOG_v4.md
   * 2019-02-22 02:35  **5.0.1**  fix dep
      * 7757363 squizlabs/php_codesniffer should be in require-dev
      * 6ac0c99 Update .rmt.yml
   * 2019-02-22 02:24  **5.0.0**  initial release
      * 9f85206 Update .rmt.yml
      * 064a3b3 Add renderer option: outputTagAsString
      * 23c59f8 Update jfcherng/php-sequence-matcher ^3.0
      * 1eb6bbb Update example/old_file.txt and the new one.
      * b9f2f7b Fix AbstractHtml::expandTabs() $tabSize undeclared
      * 469abb7 Fix problem caused by in OP_EQ, it still may be $old !== $new
      * 82ed9ca AbstractHtml::expandTabs() add argument: $onlyLeadingTabs
      * dd6d8dc Code tidy
      * 4750267 Move upgrading guide from readme to other files
      * 12b6024 Separate CHANGELOG by major version
      * ebbb509 Update readme
      * ef735c9 Code tidy
      * 4518927 Uniform more "from, to" into "old, new"
      * 079a5ec Update readme
      * dbcf0b8 Update readme
      * b84bef5 Remove deprecated APIs
      * 5330cd2 Uniform the term "base, changed" into "old, new"