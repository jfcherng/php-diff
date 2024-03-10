
## VERSION 6  UNIFIED

 * Version **6.10** - feat: add methods to get diff statistics
   * 2024-03-11 01:53  **6.10.17**  remove debug msg
      * b496ddf fix: remove debug print
      * dff8047 chore: tidy codes
      * 6650347 fix: test case wrong PHP syntax
   * 2024-03-08 02:00  **6.10.16**  fix Differ::getStatistics()
      * 297d6a9 fix: Differ::getStatistics() not working when no difference
   * 2024-03-05 17:05  **6.10.15**  new differ option: fullContextIfIdentical
      * 42dd214 chore: fix php-cs-fixer deprecated options
      * 239c68f feat: add new differ option: fullContextIfIdentical (#79)
      * 5050622 chore: tidy codes
      * d4e997c chore: ignore example/demo_web.php from php-cs-fixer
      * ab98b77 chore: update deps
      * 5a90e78 chore: fix typos (#75)
   * 2023-06-15 20:31  **6.10.14**  add translation: cze
      * 4f27313 docs: update translation readme
      * 163371b chore: add traslation Czech (#74)
   * 2023-06-05 19:44  **6.10.13**  add translation: nob
      * 286d4ae chore: update deps
      * 1f71299 chore: Norwegian translation (#73)
   * 2023-05-21 16:10  **6.10.12**  max sequence length
      * c71cd9f feat: add new differOption: lengthLimit
      * 07f448f chore: update deps
   * 2023-03-16 03:23  **6.10.11**  (Combined) fix unmerge-able block
      * 3582a09 fix: (Combined) unmerge-able block shouldn't be repeated
      * 63bbde6 docs: fix readme badge
   * 2023-03-11 15:31  **6.10.10**  tidy codes
      * d19ffb8 refactor: move "ignoreLineEnding" implementation to sequence matcher
      * e2c353a test: tidy codes
      * 0492991 chore: update deps
      * 658c9ff chore: fix typos
      * 927f5b7 chore: only test against PHP 71/72/73
   * 2023-03-10 20:56  **6.10.9**  add differ option: ignoreLineEnding
      * 9131af6 feat: add differ option: ignoreLineEnding
      * 74e1878 chore: move test files
      * 7b0c11c chore: update deps
   * 2023-01-19 00:51  **6.10.8**  override language
      * 3a99872 feat: provide a way to override an existing language
      * 8ab946c chore: update deps
      * 4f572b2 style: format readme with prettier
      * e85c214 style: reduce indentation for the demo codes
      * ad0751b test: test against PHP 8.1
   * 2022-09-26 22:31  **6.10.7**  fix: side-by-side column too narrow
      * 8f1d423 fix: side-by-side table column may be too narrow
   * 2022-09-25 22:34  **6.10.6**  new rendererOption: spaceToHtmlTag
      * d78904a style: make CI happy
      * 4180e7c feat: new rendererOption: spaceToHtmlTag (#60)
      * 702406f revert: partially revert ef08158
      * 7ead73d fix: select the code only (#59)
      * ca62d1d fix: wrong visual tabSize on browser
      * 8d71a3f chore: fix typo/spelling (#57)
      * e0b405d chore: update deps
      * ab8403d chore: update CI deps
   * 2022-08-07 21:31  **6.10.5**  fix: Combined::renderArray isn't working properly
      * 81e393d fix: php71 compatibility
      * 666dc70 chore: update deps
      * a00dc93 test: update tests
      * 598f0f4 fix: Combined::renderArray isn't working properly
   * 2022-04-03 00:36  **6.10.4**  Update upstream
      * 1f6a5d3 chore: update deps
   * 2022-03-29 18:58  **6.10.3**  Port patches
      * 27c4a0d chore: prepare for branch
      * 5be3f5a chore: update deps
      * d231e14 feat: add Italian translation (#53)
      * 91bfb23 chore: remove "todo" from test data
      * 3d54531 chore: license year +1
      * acd2814 Ukr language (#50)
      * d55fd28 chore: remove "@todo" in readme
      * 8130948 docs: revise instructions for running demo
   * 2021-09-02 12:03  **6.10.2**  Bump deps
      * bf18634 chore: bump "jfcherng/php-sequence-matcher" 3.2.8
   * 2021-09-02 11:58  **6.10.1**  Bump deps
      * 795555c chore: bump "jfcherng/php-sequence-matcher" 3.2.7
      * 814782b chore: add RMT into ignore-export
      * 98fbe77 chore: update demo js/css URLs
      * 809ea0f docs: update badegs
      * 17d912e style: fix some long lines
      * 7e9a001 style: add visibility to public consts
      * 2a28fe1 chore: update deps
   * 2021-03-19 17:20  **6.10.0**  initial release
      * 56eee87 chore: add liip/rmt as a dev dependency
      * 4b170cd feat: add methods to get diff statistics
      * d95e0b8 chore: update deps
      * 4c6e2ab chore: update license year
      * f40467c chore: allow phan/phan ^4
      * bd322af fix: deprecated method in .php_cs
      * ab25d01 chore(ci): fix php-cs-fixer rule as of 2.17.0
      * c0d13c5 chore: update deps

 * Version **6.9** - add option: showHeader
   * 2020-12-08 07:55  **6.9.0**  initial release
      * 9d98db2 chore: exclude "tests/IgnoreWhitespaceTest.php" from formatting
      * 8f33ca2 style: php-cs-fix
      * 5e5f038 fix: undefined index 'showHeader'
      * 5f0dd2d feat: add option: showHeader
      * 863fbe1 chore(ci): real test against PHP 8.0
      * 7e830bf chore: update deps
      * 4576720 chore: update php-cs-fixer 2.17.0
      * 3fede5b chore(ci): test against PHP 8.0 as well
      * a342715 docs: fix typo
      * 6ec0dd7 docs: add description links for Unified/Context diff formats

 * Version **6.8** - new renderer: JsonText
   * 2020-10-29 18:49  **6.8.0**  initial release
      * 33339b8 chore: update deps
      * efa7217 feat: add renderer: JsonText
      * f7dac30 chore: update frontend Prism 1.22.0
      * 3494b40 docs: update permanent link
      * fceef80 docs: fix typos
      * 2cdcb6b chore: nits
      * ef66d12 chore: udpate deps
      * 8f765ec chore: Composer 2 no longer needs hirak/prestissimo
      * 14fe79b chore: update deps

 * Version **6.7** - feat: support colorized CLI output for text renderers
   * 2020-08-23 18:52  **6.7.7**  fix a regression about the last diff block
      * 2ce0540 refactor: migrate to jfcherng/php-sequence-matcher 3.2.5
      * 29115e2 chore: update deps
      * f7f6e4d style: run php-cs-fix
      * ba2fc9d chore: do not check/fix coding style for tests/data/
      * 8d4c888 fix: sytnax error for PHP 7.1
      * aa34ce3 test: add failing test (#36)
   * 2020-08-22 19:08  **6.7.6**  fix "ignoreWhitespaces" not working
      * dd6a211 fix: "ignoreWhitespaces" doesn't produce the same result with GNU diff's
      * dd96f78 chore: update deps
      * 0fd52fb test: fixup: follow GNU's diff output
      * f8c08d0 Failing test for ignoreWhitespace
      * f0ed073 fix: use "static" to call non-static methods
   * 2020-08-10 23:39  **6.7.5**  fix \r visualization in Combined renderer
      * 35a2502 fix: \r shouldn't be visualized like a newline in Combined renderer
      * cb2bed2 chore: update deps
   * 2020-07-06 11:10  **6.7.4**  fix HTML escape for Combined renderer
      * 762238b chore: update deps
      * d32c7b6 fix: OP_EQ lines are not escaped in Combined renderer
      * ef6dcb4 test: make phan happy
      * dc2db03 chore: update readme badge
      * d143c26 chore(ci): migrate CI from Travis to GitHub Actions
   * 2020-05-28 03:22  **6.7.3**  allow PHP 8
      * 3ab8a9e chore: update deps
      * 1b931eb chore: Composer PHP constrain >=7.1.3
      * 37036c2 chore: add some comments
   * 2020-04-27 01:49  **6.7.2**  fix Combined internal HTML formatting
      * 8f8d919 chore: update deps
      * e172e4a fix: unexpected Combined output due to HTML formatting
      * 8011802 chore: rename example/demo.php -> example/demo_web.php
      * 444342d refactor: tidy codes
   * 2020-04-07 02:18  **6.7.1**  refactor: colorization with jfcherng/php-color-output
      * 9c3da80 refactor: colorization with jfcherng/php-color-output
      * ea2d43d docs: adjust demo colors
   * 2020-04-06 20:29  **6.7.0**  initial release
      * 40f0e74 test: fix test for CLI color output
      * ba9dd9e chore: update deps
      * 857f4b0 fix: make phan happy
      * 129d51e docs: add example/demo_cli.php
      * dbec4b8 feat: support colorized CLI output for text renderers
      * 007abaf chore: update deps

 * Version **6.6** - Add new renderer option: wordGlues
   * 2020-03-31 23:53  **6.6.4**  add Spanish translation
      * 842b4af feat: add Spanish translation (#26)
   * 2020-03-30 14:22  **6.6.3**  Just a few refactoring
      * 29dca67 test: add tests for Word line renderer
      * 355952f refactor: Word line renderer uses PREG_SPLIT_NO_EMPTY
      * 68b530e refactor: tidy codes
      * 492f53c chore: add some comments
   * 2020-03-30 00:45  **6.6.2**  Fix wordGlues
      * 7ddbb79 fix: "wordGlues" works wrongly under some circumstances (#25)
      * 16780d3 fix(CSS): better strip alignment effect
   * 2020-03-20 06:41  **6.6.1**  Add new language: German (deu)
      * 00b9a03 German language file
      * 9f1ae6b chore: update deps
      * 92e8f25 docs: update readme badges to fit markdown style
      * 0615509 refactor: tidy codes
   * 2020-03-12 19:53  **6.6.0**  initial release
      * 05a118a Update deps
      * 2b68ddc Update screenshots
      * d227113 Add new renderer option: wordGlues
      * d24c6ae Tidy codes

 * Version **6.5** - New renderer: Combined
   * 2020-03-11 16:52  **6.5.7**  Add Json renderer option: jsonEncodeFlags
      * 9a00a55 Add Json renderer option: jsonEncodeFlags
      * cb54261 Tidy codes
   * 2020-03-09 16:08  **6.5.6**  Add Combined renderer option: mergeThreshold
      * 2f85968 Tidy codes (Inline/SideBySide/Combined)
      * e39d674 Tidy codes
      * d643094 Add Combined renderer option: mergeThreshold
      * 92af433 Add more test cases
      * 570fd51 Update deps
   * 2020-03-08 00:42  **6.5.5**  Combined: do not merge blocks which change too much
      * f4f8b25 Tidy codes
      * 273d1b4 Combined: do not merge blocks which change too much
      * ac5e1b1 Fix CSS table text alignment for very long lines
      * 5046910 Tidy codes
      * 45c0b24 Do not trim trailing whitespaces for .txt files
   * 2020-03-07 18:02  **6.5.4**  Various Context/Unified fixes
      * 4484193 Tidy codes
      * 0a91891 Add some textual renderer tests
      * 3fdad9c Fix Context/Unified output for EOL at EOF
      * 8ab502f Fix Unified line numbers when context is 0
      * 795628f Fix all-equal hunk content should be omitted in Context output
      * 0f2e39a Fix there are extra leading/trailing lines when context is set to 0
      * 973fecb Add some textual renderer tests
      * 232356f Fix DiffHelper::calculateFiles() for empty file
      * 899600c Tidy codes
      * a81078f Add some comments
   * 2020-03-06 01:29  **6.5.3**  Improve Combined renderer output for newlines
      * 745199a Update deps
      * e531cdc Combined: better boundary newline chars visualization
      * bf09969 Tidy codes
      * 42366b1 Add "run-script" to custom composer commands
   * 2020-03-05 14:14  **6.5.2**  Fix multiline replaced lines in the Combined renderer
      * 5c93db2 Stripe background should only work in SideBySide
      * ea45be3 Fix "\n" should be "<br>" in Combined renderer output
      * d65c878 Use Prism.js to highlight diff example demos
      * c4e7d7b Tidy codes
   * 2020-02-29 02:30  **6.5.1**  Allow overriding SCSS variables
      * ec4583c Allow overriding SCSS variables
      * bb5203b Simplify codes for merging lines in Combined renderer
      * 6da9215 Update example readme
      * a0b906b Prefer the new if we can only show either old or new
   * 2020-02-28 22:56  **6.5.0**  initial release
      * 98fe251 Update deps
      * 2de0300 Update readme
      * a6ff11a Finish the HTML Combined renderer
      * 2cf8e84 New Renderer (#22)
      * 2277a98 Improve type docs and variable naming
      * d212f41 Update docs
      * 7a45667 Tidy codes
      * e96e971 Rename some "tag" to "op"
      * 2dd547c Update deps

 * Version **6.4** - Add renderer option: lineNumbers
   * 2020-02-25 04:22  **6.4.7**  Fixed SideBySide stripe background
      * f395759 SideBySide stripes use "background-attachment: fixed;"
      * a93f7fb Update SideBySide screenshot
   * 2020-02-24 18:52  **6.4.6**  Align stripes
      * bdc3036 Make SideBySide stripe background aligned across lines
   * 2020-02-24 14:47  **6.4.5**  CSS tweaks
      * 36799b9 SideBySide shows a stripe for nonexistent lines
      * 6b7e36b Update readme
      * 8b89bbc Add a const for indicating showing all contexts
      * bd2c868 Make empty cells have height via CSS
      * 3ae6da1 Make coding thoughts more clear
      * 0394a74 Tidy Codes
      * 7093932 Update SideBySide screenshot
      * abb8955 Improve SideBySide CSS for nonexistent blocks
      * 8cb05e8 Add keywords to composer.json
      * f061297 Update example/README.md
      * f861a3f Add more cases to example text sources
      * 34786fa Update example/README.md
   * 2020-02-13 14:16  **6.4.4**  Fix SideBySide wrong new line number
      * 7397cbe Tidy codes
      * 215209d Fix SideBySide wrong new line number
      * d91221d Fix documentation for outputTagAsString
      * a7c3222 Add example image for SideBySide without line numbers
   * 2020-02-10 05:40  **6.4.3**  Better SideBySide output without line numbers
      * 067c715 Merge SideBySide insert/delete columns when lineNumbers is false
   * 2020-02-09 18:27  **6.4.2**  Fix SidebySide fatal error
      * 4e74cc5 Fix SideBySide renderer fatal error
   * 2020-02-09 17:43  **6.4.1**  Fix typo
      * f2afe14 Fix typo: Woker -> Worker
   * 2020-02-07 17:51  **6.4.0**  initial release
      * 802a7cb Tidy codes
      * 52045f3 Update documents
      * 69f42a1 lineNumber option (#19)
      * 1140ec2 Fix phan errors
      * 10cc545 Fix documentation about rendering from JSON diff
      * 4072992 Update deps
      * 90a86e0 ++year

 * Version **6.3** - Render HTML with JSON result
   * 2020-01-16 13:00  **6.3.2**  Add French (fra) translation
      * 41d571f Add French language file
   * 2020-01-12 01:08  **6.3.1**  Remove dep: nicmart/string-template
      * 82259d0 Update dev require
      * d83f3ac Update readme [skip ci]
      * b36aeec Revert "Use string template engine to render language translations"
      * 0a991e7 Update tests
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