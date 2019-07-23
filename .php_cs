<?php

/**
 * 使用说明
 *
 * 基于 php-cs-fixer 插件，请先使用composer安装此插件
 *
 * 1、Preferences -> Tools -> External Tools 添加工具
 * 2、添加 php-cs-fixer 工具，参如如下：
 *      Name: php-fixer
 *      Description: php-fixer
 *      Program: /{项目根目录}/vendor/friendsofphp/php-cs-fixer/php-cs-fixer
 *      Arguments: --config=$ProjectFileDir$/.php_cs --verbose fix "$FileDir$/$FileName$"
 *      Working directory: 同Program
 * 3、Keymap -> External Tools -> php-fixer 设置快捷键 Alt+f
 * 4、打开要格式化的文件，按 Alt+f 保存即可
 */


define('SOFT_NAME', 'WeEngine Document System');

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
	'indentation_type'                           => true,
	'header_comment' => [
	    'comment_type' => 'PHPDoc',
	    'header' => SOFT_NAME . " \r\n\r\n(c) We7Team 2019 <https://www.w7.cc> \r\n\r\nThis is not a free software \r\nUsing it under the license terms\r\nvisited https://www.w7.cc for more details",
	],
	//'braces'                                     => ['position_after_anonymous_constructs' => 'same'], //设置大括号换行，暂时根本Psr
	//'binary_operator_spaces'                     => ['default' => 'align_single_space'], //等号对齐、数字箭头符号对齐
);
return PhpCsFixer\Config::create()
	->setRules($fixers)
	->setFinder($finder)
	->setIndent("\t")
	->setUsingCache(false);
