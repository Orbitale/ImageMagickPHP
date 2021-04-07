<?php

$header = <<<'HEADER'
This file is part of the OrbitaleImageMagickPHP package.

(c) Alexandre Rock Ancelet <alex@orbitale.io>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'vendor',
        'build',
        '.idea',
    ])
    ->notName('bootstrap.php')
    ->in([
        __DIR__.'/',
    ])
;

return PhpCsFixer\Config::create()
    ->setRules([
        'header_comment' => [
            'header' => $header,
        ],
        // Enabled rules
        '@Symfony'                        => true,
        '@Symfony:risky'                  => true,
        '@PHP56Migration'                 => true,
        '@PHP70Migration'                 => true,
        '@PHP70Migration:risky'           => true,
        '@PHP71Migration'                 => true,
        '@PHP71Migration:risky'           => true,
        'compact_nullable_typehint'       => true,
        'fully_qualified_strict_types'    => true,
        'heredoc_to_nowdoc'               => true,
        'linebreak_after_opening_tag'     => true,
        'logical_operators'               => true,
        'native_function_invocation'      => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor'             => true,
        'no_short_echo_tag'               => true,
        'no_superfluous_phpdoc_tags'      => true,
        'no_useless_else'                 => true,
        'no_useless_return'               => true,
        'ordered_imports'                 => true,
        'simplified_null_return'          => true,
        'strict_param'                    => true,
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'static',
        ],
        'array_syntax'                    => [
            'syntax' => 'short',
        ],
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'new_line_for_chained_calls',
        ],

        // Disabled rules
        'increment_style' => false,         // Because "++$i" is not always necessary…
        'non_printable_character' => false, // Because I love using non breakable spaces in test methods ♥
    ])
    ->setRiskyAllowed(true)
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setUsingCache(true)
    ->setFinder($finder)
;
