<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'vendor',
        'bot/.venv',
    ])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        // Base style
        '@PSR12' => true,

        // ---------------- My Style ----------------

        // Prefer single quotes
        'single_quote' => true,

        // Always use short array syntax []
        'array_syntax' => [
            'syntax' => 'short',
        ],

        // Also use short destructuring syntax []
        'list_syntax' => [
            'syntax' => 'short',
        ],

        // $a . $b
        'concat_space' => [
            'spacing' => 'one',
        ],

        // $a = $b + $c
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],

        // $object->method() (no whitespace around -> or ?->)
        'object_operator_without_whitespace' => true,

        // Sort use statements alphabetically
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],

        // Remove unused use statements
        'no_unused_imports' => true,

        // if (...) {
        // function foo() {
        'braces_position' => [
            'control_structures_opening_brace' => 'same_line',
            'functions_opening_brace' => 'same_line',
        ],

        // No trailing comma on single-line lists
        'no_trailing_comma_in_singleline' => true,

        'function_declaration' => [
            'closure_fn_spacing' => 'one',
            'closure_function_spacing' => 'one',
            'trailing_comma_single_line' => false,
        ],

        // ---------------- Modernization ----------------

        // Add: declare(strict_types=1);
        'declare_strict_types' => [
            'strategy' => 'add_when_missing',
        ],

        // (integer) -> (int), etc.
        'modernize_types_casting' => true,

        // Standardize built-in function/type casing
        'native_function_casing' => true,
        'native_type_declaration_casing' => true,

        // Replace old aliases such as sizeof() where applicable
        'no_alias_functions' => true,

        // Merge compatible consecutive isset()/unset()
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,

        // $a !== null ? $a : $b -> $a ?? $b where safe
        'ternary_to_null_coalescing' => true,
    ])
    ->setFinder($finder);
