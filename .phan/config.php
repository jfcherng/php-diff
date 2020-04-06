<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 */
return [

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        'src',
        'vendor/jfcherng/php-mb-string/src',
        'vendor/jfcherng/php-sequence-matcher/src',
        'vendor/symfony/console',
    ],

    // A directory list that defines files that will be excluded
    // from static analysis, but whose class and method
    // information should be included.
    //
    // Generally, you'll want to include the directories for
    // third-party code (such as "vendor/") in this list.
    //
    // n.b.: If you'd like to parse but not analyze 3rd
    //       party code, directories containing that code
    //       should be added to the `directory_list` as
    //       to `exclude_analysis_directory_list`.
    "exclude_analysis_directory_list" => [
        'vendor',
    ],

    // A regular expression to match files to be excluded
    // from parsing and analysis and will not be read at all.
    //
    // This is useful for excluding groups of test or example
    // directories/files, unanalyzable files, or files that
    // can't be removed for whatever reason.
    // (e.g. '@Test\.php$@', or '@vendor/.*/(tests|Tests)/@')
    'exclude_file_regex' => '@(src|vendor)/.*/([tT]ests)/@',

    // A file list that defines files that will be excluded
    // from parsing and analysis and will not be read at all.
    //
    // This is useful for excluding hopelessly unanalyzable
    // files that can't be removed for whatever reason.
    'exclude_file_list' => [],

    // If true, missing properties will be created when
    // they are first seen. If false, we'll report an
    // error message.
    "allow_missing_properties" => false,

    // Backwards Compatibility Checking
    'backward_compatibility_checks' => false,

    // Run a quick version of checks that takes less time
    "quick_mode" => false,

    // Moving minimum_severity in your config
    // from 10 (severe) to 5 (normal) or even 0 (low) if you love fixing bugs.
    "minimum_severity" => 0,

    // Add any issue types (such as 'PhanUndeclaredMethod')
    // to this black-list to inhibit them from being reported.
    'suppress_issue_types' => [
        // 'PhanTypeArraySuspicious',
        // 'PhanTypeMismatchProperty',
        // 'PhanUnanalyzable',
    ],

    // If empty, no filter against issues types will be applied.
    // If this white-list is non-empty, only issues within the list
    // will be emitted by Phan.
    'whitelist_issue_types' => [
        // 'PhanAccessClassConstantInternal',
        // 'PhanAccessClassConstantPrivate',
        // 'PhanAccessClassConstantProtected',
        // 'PhanAccessClassInternal',
        // 'PhanAccessConstantInternal',
        // 'PhanAccessMethodInternal',
        // 'PhanAccessMethodPrivate',
        // 'PhanAccessMethodPrivateWithCallMagicMethod',
        // 'PhanAccessMethodProtected',
        // 'PhanAccessMethodProtectedWithCallMagicMethod',
        // 'PhanAccessNonStaticToStatic',
        // 'PhanAccessOwnConstructor',
        // 'PhanAccessPropertyInternal',
        // 'PhanAccessPropertyPrivate',
        // 'PhanAccessPropertyProtected',
        // 'PhanAccessPropertyStaticAsNonStatic',
        // 'PhanAccessSignatureMismatch',
        // 'PhanAccessSignatureMismatchInternal',
        // 'PhanAccessStaticToNonStatic',
        // 'PhanClassContainsAbstractMethod',
        // 'PhanClassContainsAbstractMethodInternal',
        // 'PhanCommentParamOnEmptyParamList',
        // 'PhanCommentParamWithoutRealParam',
        // 'PhanCompatibleExpressionPHP7',
        // 'PhanCompatiblePHP7',
        // 'PhanContextNotObject',
        // 'PhanDeprecatedClass',
        // 'PhanDeprecatedFunction',
        // 'PhanDeprecatedFunctionInternal',
        // 'PhanDeprecatedInterface',
        // 'PhanDeprecatedProperty',
        // 'PhanDeprecatedTrait',
        // 'PhanEmptyFile',
        // 'PhanGenericConstructorTypes',
        // 'PhanGenericGlobalVariable',
        // 'PhanIncompatibleCompositionMethod',
        // 'PhanIncompatibleCompositionProp',
        // 'PhanInvalidCommentForDeclarationType',
        // 'PhanMismatchVariadicComment',
        // 'PhanMismatchVariadicParam',
        // 'PhanMisspelledAnnotation',
        // 'PhanNonClassMethodCall',
        // 'PhanNoopArray',
        // 'PhanNoopClosure',
        // 'PhanNoopConstant',
        // 'PhanNoopProperty',
        // 'PhanNoopVariable',
        // 'PhanParamRedefined',
        // 'PhanParamReqAfterOpt',
        // 'PhanParamSignatureMismatch',
        // 'PhanParamSignatureMismatchInternal',
        // 'PhanParamSignaturePHPDocMismatchHasNoParamType',
        // 'PhanParamSignaturePHPDocMismatchHasParamType',
        // 'PhanParamSignaturePHPDocMismatchParamIsNotReference',
        // 'PhanParamSignaturePHPDocMismatchParamIsReference',
        // 'PhanParamSignaturePHPDocMismatchParamNotVariadic',
        // 'PhanParamSignaturePHPDocMismatchParamType',
        // 'PhanParamSignaturePHPDocMismatchParamVariadic',
        // 'PhanParamSignaturePHPDocMismatchReturnType',
        // 'PhanParamSignaturePHPDocMismatchTooFewParameters',
        // 'PhanParamSignaturePHPDocMismatchTooManyRequiredParameters',
        // 'PhanParamSignatureRealMismatchHasNoParamType',
        // 'PhanParamSignatureRealMismatchHasNoParamTypeInternal',
        // 'PhanParamSignatureRealMismatchHasParamType',
        // 'PhanParamSignatureRealMismatchHasParamTypeInternal',
        // 'PhanParamSignatureRealMismatchParamIsNotReference',
        // 'PhanParamSignatureRealMismatchParamIsNotReferenceInternal',
        // 'PhanParamSignatureRealMismatchParamIsReference',
        // 'PhanParamSignatureRealMismatchParamIsReferenceInternal',
        // 'PhanParamSignatureRealMismatchParamNotVariadic',
        // 'PhanParamSignatureRealMismatchParamNotVariadicInternal',
        // 'PhanParamSignatureRealMismatchParamType',
        // 'PhanParamSignatureRealMismatchParamTypeInternal',
        // 'PhanParamSignatureRealMismatchParamVariadic',
        // 'PhanParamSignatureRealMismatchParamVariadicInternal',
        // 'PhanParamSignatureRealMismatchReturnType',
        // 'PhanParamSignatureRealMismatchReturnTypeInternal',
        // 'PhanParamSignatureRealMismatchTooFewParameters',
        // 'PhanParamSignatureRealMismatchTooFewParametersInternal',
        // 'PhanParamSignatureRealMismatchTooManyRequiredParameters',
        // 'PhanParamSignatureRealMismatchTooManyRequiredParametersInternal',
        // 'PhanParamSpecial1',
        // 'PhanParamSpecial2',
        // 'PhanParamSpecial3',
        // 'PhanParamSpecial4',
        // 'PhanParamTooFew',
        // 'PhanParamTooFewInternal',
        // 'PhanParamTooMany',
        // 'PhanParamTooManyInternal',
        // 'PhanParamTypeMismatch',
        // 'PhanParentlessClass',
        // 'PhanRedefineClass',
        // 'PhanRedefineClassAlias',
        // 'PhanRedefineClassInternal',
        // 'PhanRedefineFunction',
        // 'PhanRedefineFunctionInternal',
        // 'PhanRequiredTraitNotAdded',
        // 'PhanStaticCallToNonStatic',
        // 'PhanSyntaxError',
        // 'PhanTemplateTypeConstant',
        // 'PhanTemplateTypeStaticMethod',
        // 'PhanTemplateTypeStaticProperty',
        // 'PhanTraitParentReference',
        // 'PhanTypeArrayOperator',
        // 'PhanTypeArraySuspicious',
        // 'PhanTypeComparisonFromArray',
        // 'PhanTypeComparisonToArray',
        // 'PhanTypeConversionFromArray',
        // 'PhanTypeInstantiateAbstract',
        // 'PhanTypeInstantiateInterface',
        // 'PhanTypeInvalidClosureScope',
        // 'PhanTypeInvalidLeftOperand',
        // 'PhanTypeInvalidRightOperand',
        // 'PhanTypeMismatchArgument',
        // 'PhanTypeMismatchArgumentInternal',
        // 'PhanTypeMismatchDeclaredParam',
        // 'PhanTypeMismatchDeclaredReturn',
        // 'PhanTypeMismatchDefault',
        // 'PhanTypeMismatchForeach',
        // 'PhanTypeMismatchProperty',
        // 'PhanTypeMismatchReturn',
        // 'PhanTypeMissingReturn',
        // 'PhanTypeNonVarPassByRef',
        // 'PhanTypeParentConstructorCalled',
        // 'PhanTypeSuspiciousIndirectVariable',
        // 'PhanTypeVoidAssignment',
        // 'PhanUnanalyzable',
        // 'PhanUndeclaredAliasedMethodOfTrait',
        // 'PhanUndeclaredClass',
        // 'PhanUndeclaredClassAliasOriginal',
        // 'PhanUndeclaredClassCatch',
        // 'PhanUndeclaredClassConstant',
        // 'PhanUndeclaredClassInstanceof',
        // 'PhanUndeclaredClassMethod',
        // 'PhanUndeclaredClassReference',
        // 'PhanUndeclaredClosureScope',
        // 'PhanUndeclaredConstant',
        // 'PhanUndeclaredExtendedClass',
        // 'PhanUndeclaredFunction',
        // 'PhanUndeclaredInterface',
        // 'PhanUndeclaredMethod',
        // 'PhanUndeclaredProperty',
        // 'PhanUndeclaredStaticMethod',
        // 'PhanUndeclaredStaticProperty',
        // 'PhanUndeclaredTrait',
        // 'PhanUndeclaredTypeParameter',
        // 'PhanUndeclaredTypeProperty',
        // 'PhanUndeclaredTypeReturnType',
        // 'PhanUndeclaredVariable',
        // 'PhanUndeclaredVariableDim',
        // 'PhanUnextractableAnnotation',
        // 'PhanUnextractableAnnotationPart',
        // 'PhanUnreferencedClass',
        // 'PhanUnreferencedConstant',
        // 'PhanUnreferencedMethod',
        // 'PhanUnreferencedProperty',
        // 'PhanVariableUseClause',
    ],

];

