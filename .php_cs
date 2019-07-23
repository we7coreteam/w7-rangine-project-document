<?php

$finder = PhpCsFixer\Finder::create()
	->files()
	->name('*.php')
	->exclude('vendor')
	->in(__DIR__)
	->ignoreDotFiles(true)
	->ignoreVCS(true);

$fixers = array(
	'@PSR2'                                      => true,
	'single_quote'                               => true, //简单字符串应该使用单引号代替双引号；
	'no_unused_imports'                          => true, //删除没用到的use
	'no_singleline_whitespace_before_semicolons' => true, //禁止只有单行空格和分号的写法；
	'no_empty_statement'                         => true, //多余的分号
	'no_extra_consecutive_blank_lines'           => true, //多余空白行
	'no_blank_lines_after_class_opening'         => true, //类开始标签后不应该有空白行；
	'include'                                    => true, //include 和文件路径之间需要有一个空格，文件路径不需要用括号括起来；
	'no_trailing_comma_in_list_call'             => true, //删除 list 语句中多余的逗号；
	'no_leading_namespace_whitespace'            => true, //命名空间前面不应该有空格；
	'standardize_not_equals'                     => true, //使用 <> 代替 !=；
	'blank_line_after_opening_tag'               => true, //PHP开始标记后换行
	//'braces'                                     => ['position_after_anonymous_constructs' => 'same'], //设置大括号换行，暂时根本Psr
	//'binary_operator_spaces'                     => ['default' => 'align_single_space'], //等号对齐、数字箭头符号对齐
);
return PhpCsFixer\Config::create()
	->setRules($fixers)
	->setFinder($finder)
	->setIndent("\t")
	->setUsingCache(false);
