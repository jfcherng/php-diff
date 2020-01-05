
## VERSION 6  UNIFIED

 * Version **6.3** - Render HTML with JSON result
   * 2020-01-05 16:21  **6.3.0**  initial release
      * 91a6180 Update deps
      * c713a0a Update examples and README
      * 3c6d9a3 Variables renaming (example/demo.php)
      * ce71a93 Add tests for \Jfcherng\Diff\Renderer\AbstractRenderer::renderArray
      * f497539 Tidy codes
      * caec927 Require jfcherng/php-sequence-matcher ^3.2
      * 3111605 Let text renderers throw exception for renderArray()
      * 8235504 Ability to render HTML from JSON (#17)

 * Version **6.2** - Add renderer option: resultForIdenticals
   * 2020-01-05 01:19  **6.2.1**  Add Turkish translation
      * f86585a Update deps
      * 858625f Create tur.json
      * eae8262 Add some @return static
      * caaaba7 Add .phpstorm.meta.php
      * 1067679 Update .travis.yml to PHP 7.4 stable
      * 6a5cd7b $ composer fix
      * d69a153 Update deps
      * 3e1b965 Use string template engine to render language translations
   * 2019-10-12 05:33  **6.2.0**  initial release
      * f7aba88 Optimize RendererFactory::resolveRenderer()
      * 288adb2 Fix typos
      * 601cf86 Throw an exception for invalid "resultForIdenticals"
      * 9a71712 Add renderer option: resultForIdenticals
      * 53ac441 nits: tests
      * d1a7479 Update deps
      * 31b4916 Remove unnecessary CSS
      * f05f4a3 Tidy files

 * Version **6.1** - Resolve CSS conflict
   * 2019-10-11 06:34  **6.1.2**  Add Portuguese translation
      * a07dea9 Rename pt_BR.json -> por.json
      * d89a7b3 Replace "switch" statements with callback function tables
      * 5b9a828 Create pt_BR.json
      * 896df76 Follow PSR-12
      * 6d3ae95 Move FUNDING.yml to .github/
      * cf9032a Add test for renderer custom language array
   * 2019-09-11 00:19  **6.1.1**  Fix "language" cannot be an array
      * 98085b6 Fix renderer option "language" cannot be an array
      * 3518068 Update .gitattributes
   * 2019-09-10 17:24  **6.1.0**  initial release
      * f9400d3 Add renderer option: wrapperClasses
      * f2f613c Update readme (nits)
      * fdead6f Update readme (flat-square style badges)

 * Version **6.0** - Unified
   * 2019-09-10 14:30  **6.0.1**  Fix jpn wording
      * 00b59ef Fix jpn wording
      * d04814e Sass: replace compass/css3 functions with dart-sass built-in
      * a3b05d9 Update readme to use badges from shields.io
      * 5dbdf21 Update readme: nits
      * db4439c Slightly improve code readability
      * 682fb39 nits
      * 2b13e8e Update deps
      * 0c9b79b Add .gitattributes
      * fa9f3a6 Create FUNDING.yml
      * f81b823 Move documentation assets to the current branch
      * 4c2c11d Release of new version 5.2.2
      * 2bd27d4 Update deps
      * fa189fa Update deps
      * 6b30019 Update deps
      * abc0d8a $ composer fix
      * 7cb5a75 Update deps
      * 70f66a2 Update .travis.yml for 7.4snapshot
      * e7e1839 Update deps
      * 1cee802 Update readme
      * bf14c89 Update deps
      * c52f66b Fix typo in UPGRADING_v6.md
      * 756970a Add .editorconfig
      * e6350bc Change screenshot size
      * 55748a5 Update deps
      * d8644db Update deps
      * 03b0c55 Freeze documentation assets for v5
      * bd61843 Freeze documentation assets for v4
   * 2019-03-20 11:31  **6.0.0**  initial release
      * 46d8e1b Update .rmt.yml
      * f3f209c Fix tests
      * c625cb3 Revise UPGRADING_v6.md
      * 6807e65 Let Differ manages its own state, i.e., finalize()
      * ae309c5 Fix some grammar problems in UPGRADING_v6.md
      * 9fa89dd Add RendererInterface::getResultForIdenticals()
      * b781125 Release of new version 5.2.0
      * 57fa3cd nits
      * 926f19f $ php-cs-fixer fix
      * 89ec714 [php-cs-fixer] Add new rules: @PhpCsFixer, @PhpCsFixer:risky
      * 83830c7 Improve JSON decoding in Language::getTranslationsByLanguage()
      * 3b40e42 Release of new version 4.2.3
      * b39f29a Make Language::getTranslationsByLanguage() safer
      * 846d0ca Rename the term "template" to "renderer"
      * 8439977 Use native class name
      * 5330a8e Release of new version 4.2.2
      * 4979a53 nits
      * ffaefef Fix phan error
      * dc26604 Rename Differ::groupedCodes to Differ::groupedOpcodes
      * ba570f8 Make Differ work in a DI way in Renderer internally
      * 177146e Rename Diff to Differ
      * 0a30b27 Renderer::render() rather than Diff::render()
      * deb9ed3 Revise phpdoc
      * d8606b7 Release of new version 5.1.0