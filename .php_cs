<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('docs/*')
    ->notPath('vendor')
    ->in([
        __DIR__.'/src',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => ['statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try']],
        'cast_spaces' => ['space' => 'single'],
        'class_attributes_separation' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'none'],
        'dir_constant' => true, // risky
        'ereg_to_preg' => true, // risky
        'escape_implicit_backslashes' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'fully_qualified_strict_types' => true,
        'function_to_constant' => true,
        'function_typehint_space' => true,
        'general_phpdoc_annotation_remove' => ['author', 'class', 'namespace'],
        'heredoc_indentation' => true,
        'heredoc_to_nowdoc' => true,
        'implode_call' => true, // risky
        'include' => true,
        'is_null' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'logical_operators' => true,
        'lowercase_cast' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'mb_str_functions' => true,
        'method_chaining_indentation' => true,
        'modernize_types_casting' => true, // risky
        'multiline_comment_opening_closing' => true,
        'multiline_whitespace_before_semicolons' => true,
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        'new_with_braces' => true,
        'no_alternative_syntax' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'break',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use',
                'useTrait',
                'use_trait',
            ],
        ],
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_php4_constructor' => true,
        'no_short_bool_cast' => true,
        'no_short_echo_tag' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_superfluous_elseif' => true,
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_trailing_whitespace_in_comment' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_unneeded_final_method' => true,
        'no_unreachable_default_argument_value' => true, // risky
        'no_unset_cast' => true,
        'no_unset_on_property' => false, // risky
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'non_printable_character' => true, // risky
        'normalize_index_brace' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'php_unit_construct' => true, // risky
        'php_unit_dedicate_assert' => true, // risky
        'php_unit_dedicate_assert_internal_type' => true, // risky
        'php_unit_expectation' => true, // risky
        'php_unit_fqcn_annotation' => true,
        'php_unit_method_casing' => ['case' => 'camel_case'], // could be 'snake_case'
        'php_unit_mock' => true, // risky
        'php_unit_mock_short_will_return' => true, // risky
        'php_unit_namespaced' => true, // risky
        'php_unit_no_expectation_annotation' => true, // risky
        'php_unit_set_up_tear_down_visibility' => true, // risky
        'php_unit_test_annotation' => ['style' => 'annotation'], // risky
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'], // risky
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
        'phpdoc_align' => [
            'align' => 'left',
            'tags' => [
                'param',
                'property',
                'property-read',
                'property-write',
                'return',
                'throws',
                'type',
                'var',
                'method',
            ],
        ],
        'phpdoc_indent' => true,
        'phpdoc_inline_tag' => true,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_scalar' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_to_comment' => false,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'phpdoc_var_annotation_correct_order' => true,
        'pow_to_exponentiation' => true,
        'psr4' => true,
        'random_api_migration' => true,
        'return_assignment' => true,
        'return_type_declaration' => true,
        'semicolon_after_instruction' => true,
        'set_type_to_cast' => true, // risky
        'short_scalar_cast' => true,
        'simplified_null_return' => true,
        'single_blank_line_before_namespace' => true,
        'single_line_comment_style' => true,
        'single_quote' => true,
        'space_after_semicolon' => true,
        'standardize_increment' => true,
        'standardize_not_equals' => true,
        'static_lambda' => true, // risky
        'switch_case_semicolon_to_colon' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_null_coalescing' => true,
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'yoda_style'=> [
            'always_move_variable'=> false,
            'equal' => false,
            'identical' => false,
            'less_and_greater' => null,
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
