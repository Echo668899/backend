<?php

// 项目根目录下执行
# vendor/bin/php-cs-fixer fix

// php -d memory_limit=2560M vendor/bin/php-cs-fixer fix  --config=.php-cs-fixer.dist.php
// composer require friendsofphp/php-cs-fixer --no-dev
// php-cs-fixer fix  --config=.php-cs-fixer.dist.php
// php-cs-fixer fix  --config=.php-cs-fixer.dist.php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app')
    ->notName('*.test.php')
    ->exclude('app/Views/Backend/default');
// 排除 default 目录
return (new PhpCsFixer\Config())
    ->setParallelConfig(new PhpCsFixer\Runner\Parallel\ParallelConfig(8, 40))
    ->setUsingCache(true)
    ->setRules([
        '@PSR12'                              => true,                  // 启用 PSR-12 规则
        'cast_spaces'                         => true,                  // 强制类型转换使用空格
        'phpdoc_trim'                         => true,                  // 去除文档注释空行
        'phpdoc_align'                        => true,                  // 对齐 PHPDoc
        'single_quote'                        => true,                  // 双引号转换单引号
        'no_empty_phpdoc'                     => true,                  // 删除空注释
        'no_useless_else'                     => true,                  // 删除无用的eles
        'no_useless_return'                   => true,                  // 删除函数末尾无用的return
        'no_unused_imports'                   => true,                  // 删除未使用的导入
        'no_empty_statement'                  => true,                  // 删除多余的分号
        'no_empty_comment'                    => true,                  // 删除无用的注释
        'heredoc_to_nowdoc'                   => true,                  // 删除配置中多余的空行和/或者空行。
        'trim_array_spaces'                   => true,                  // 删除数组首或尾随单行空格
        'array_indentation'                   => true,                  // 数组缩进
        'short_scalar_cast'                   => true,                  // boolean=>bool integer=>int double,real=>float binary=>string
        'single_line_comment_spacing'         => true,                  // 单行注释必须有适当的间距
        'visibility_required'                 => true,                  // 强制类成员的可见性
        'ordered_class_elements'              => true,                  // class elements排序
        'standardize_not_equals'              => true,                  // 标准化不等号的使用
        'no_trailing_whitespace'              => true,                  // 删除行尾空格
        'align_multiline_comment'             => true,                  // 对齐多行注释
        'ternary_operator_spaces'             => true,                  // 三元运算符周围的空格
        'ternary_to_null_coalescing'          => true,                  // 尽可能使用null合并运算符??。需要PHP> = 7.0。
        'combine_consecutive_unsets'          => true,                  // 多个unset，合并成一个
        'no_whitespace_in_blank_line'         => true,                  // 删除空白行末尾的空白
        'linebreak_after_opening_tag'         => true,                  // 在 PHP 开头标签后强制换行
        'no_blank_lines_after_phpdoc'         => true,                  // 在 PHPDoc 后禁止空行
        'no_spaces_inside_parenthesis'        => true,                  // 删除括号后内两端的空格
        'no_leading_namespace_whitespace'     => true,                  // 删除namespace声明行包含前导空格
        'whitespace_after_comma_in_array'     => true,                  // 在数组声明中，每个逗号后必须有一个空格
        'phpdoc_add_missing_param_annotation' => true,                  // 添加缺少的 Phpdoc @param参数
        'no_whitespace_before_comma_in_array' => true,                  // 删除数组声明中，每个逗号前的空格
        'array_syntax'                        => ['syntax' => 'short'], // 使用短数组语法
        'concat_space'                        => ['spacing' => 'one'],  // 字符串连接保持空格
        'constant_case'                       => ['case' => 'lower'],   // true,false,null这几个php常量转换为小写
        'phpdoc_order'                        => ['order' => ['param', 'return', 'throws']],
        'method_argument_space'               => ['on_multiline' => 'ensure_fully_multiline'],
        'phpdoc_line_span'                    => ['const' => 'single', 'property' => 'single'],
        'phpdoc_scalar'                       => ['types' => ['boolean', 'callback', 'double', 'integer', 'real', 'str']], // boolean=>bool integer=>int double,real=>float binary=>string
        'ordered_imports'                     => [
            'sort_algorithm' => 'alpha',
            'imports_order'  => ['const', 'class', 'function']
        ],
        'no_extra_blank_lines'                => [
            'tokens' => ['attribute', 'break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait',]
        ],
        'binary_operator_spaces'              => [
            'operators' => [
                '='  => 'align_single_space_minimal',
                '+=' => 'align_single_space_minimal',
                '-=' => 'align_single_space_minimal',
                '*=' => 'align_single_space_minimal',
                '/=' => 'align_single_space_minimal',
                '%=' => 'align_single_space_minimal',
                '==' => 'align_single_space_minimal',
                '=>' => 'align_single_space_minimal',
            ]
        ],
    ])
    ->setFinder($finder);
