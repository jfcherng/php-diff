
## VERSION 4  AT NOON

 * Version **4.1** - Allow negative tabSize
   * 2019-02-21 19:50  **4.1.5**  cs tweak
      * 1464d86 Update .rmt.yml
      * b3b0595 Code tidy
      * 7caae81 Update readme
      * 347935c Update deps
      * 6f8e9dd Uniform the term "$a, $b, from, to" into "old, new"
      * f7679ff Code tidy
      * 159d244 nits
      * 76675e4 Tiny regex performance increasement
      * 42eeff7 Fix 120 chars per line
      * 86f2325 Introduce squizlabs/php_codesniffer
   * 2019-02-20 18:49  **4.1.4**  Fix Diff::getText()
      * 540daf5 Fix potential boundary error in Diff::getText()
      * 2b723e7 Revert "Inline and remove Diff::getText()"
   * 2019-02-20 16:45  **4.1.3**  Fix Diff::getText()
      * a5171c8 Inline and remove Diff::getText()
      * be52d42 Fix Diff::getText() when $end is null
      * 3a6259a Code tidy
      * 1fd7935 Update option comments
   * 2019-02-20 15:04  **4.1.2**  update deps
      * df25c88 Update jfcherng/php-sequence-matcher ^2.0
      * 0c7b425 Update readme
   * 2019-02-19 19:36  **4.1.1**  fix some HTML templates
      * 0ed0e53 Fix HTML renderer should not emphasize inserted/deleted lines
      * 6e97b8a Update output example in readme
      * 80db0a0 Fix HTML special chars in JSON renderer should be escaped
      * 09a405e Code tidy
      * 6be0a0e Update deps
      * cf5bff2 AbstractHtml::expandTabs() has a default $tabSize = 4
   * 2019-02-16 18:36  **4.1.0**  initial release
      * 53e6b17 Allow renderer option tabSize < 0
      * e60fb5c Remove useless codes
      * bf85310 Add more punctuations for word-level line renderer
      * 847b795 Update readme
      * ba77110 nits
      * b13149a Update deps

 * Version **4.0** - Strip out the SequenceMatcher
   * 2019-02-08 20:17  **4.0.1**  nits
      * 8f11cb1 Fix a typo
      * 54351a6 Move factory classes to Jfcherng\Diff\Factory\
      * 6d68583 Merge ReservedConstantInterface into RendererConstant
   * 2019-02-08 10:04  **4.0.0**  initial release
      * fac4dae nits
      * b6fa08d Strip out SequenceMatcher as an external package
      * 2349bb2 Demo uses "font-family: monospace"
      * ed3f47c Temporary fix for CI
      * 538f270 Update tests
      * 11cf372 Fix typos
      * d56ad7e Use native exceptions
      * 67351d1 Update deps
      * 8b14dae Add "final" to classes non-inherited
      * ee6e5a3 Add UTF-8-ready demo
      * bf686a2 Prepare for being a new package
      * 04fb079 nits
