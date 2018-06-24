<?php

$config = PhpCsFixer\Config::create()
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setCacheFile(__DIR__ . '/.php_cs.cache')
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP71Migration' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'comment_to_phpdoc' => true,
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'fully_qualified_strict_types' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'native_constant_invocation' => false,
        'method_argument_space' => ['ensure_fully_multiline' => true],
        'no_alternative_syntax' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_null_property_initialization' => true,
        'no_short_echo_tag' => true,
        'no_superfluous_elseif' => true,
        'no_unneeded_control_parentheses' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'not_operator_with_space' => false,
        'not_operator_with_successor_space' => false,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_ordered_covers' => true,
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_strict' => true,
        'php_unit_test_class_requires_covers' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_to_comment' => false,
        'phpdoc_types_order' => true,
        'pow_to_exponentiation' => true,
        'random_api_migration' => true,
        'single_line_comment_style' => true,
        'string_line_ending' => true,
        'strict_comparison' => false,
        'strict_param' => false,
        'yoda_style' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('libs')
            ->exclude('tests/Fixtures')
            ->exclude('var')
            ->exclude('vendor')
            ->exclude('webhook')
            ->in(__DIR__)
    )
;

return $config;
