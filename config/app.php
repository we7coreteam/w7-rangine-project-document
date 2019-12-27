<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

return [
	'setting' => [
		'env' => ienv('SETTING_DEVELOPMENT', RELEASE),
		'error_reporting' => ienv('SETTING_ERROR_REPORTING', E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED^E_USER_DEPRECATED),
		'server' => ienv('SETTING_SERVERS', 'http'),
		'basedir' => [],
		'lang' => 'zh-CN',
	],
	'cache' => [
		'default' => [
			'driver' => ienv('CACHE_DEFAULT_DRIVER', 'redis'),
			'host' => ienv('CACHE_DEFAULT_HOST', '127.0.0.1'),
			'port' => ienv('CACHE_DEFAULT_PORT', '6379'),
			'timeout' => ienv('CACHE_DEFAULT_TIMEOUT', 30),
			'password' => ienv('CACHE_DEFAULT_PASSWORD', ''),
			'database' => ienv('CACHE_DEFAULT_DATABASE', '0'),
		],
	],
	'database' => [
		'default' => [
			'driver' => ienv('DATABASE_DEFAULT_DRIVER', 'mysql'),
			'database' => ienv('DATABASE_DEFAULT_DATABASE', 'document'),
			'host' => ienv('DATABASE_DEFAULT_HOST', '127.0.0.1'),
			'username' => ienv('DATABASE_DEFAULT_USERNAME', 'root'),
			'password' => ienv('DATABASE_DEFAULT_PASSWORD', 'root'),
			'charset' => 'utf8mb4',
			'collation' => 'utf8mb4_general_ci',
			'prefix' => ienv('DATABASE_DEFAULT_PREFIX', 'ims_'),
			'port' => ienv('DATABASE_DEFAULT_PORT', 3306),
			'strict' => false
		],
	],
	'view' => [
		'template_path' => [
			'public' => BASE_PATH . '/public'
		]
	],
	'session' => [
		'expires' => 86400,
		'handler' => 'db'
	],
	'cookie' => [
		'path' => ienv('SESSION_PATH', '/'),
		'http_only' => ienv('SESSION_HTTP_ONLY', false),
		'domain' => ienv('SESSION_DOMAIN', ''),
		'secure' => ienv('SESSION_SECURE', false),
		'expires' => ienv('SESSION_EXPIRES', 0),//不设置，默认取session.gc_maxlifetime配置
	],

	'pool' => [
		'database' => [
			'default' => [
				'enable' => true,
				'max' => 1000,
			],
		],
		'cache' => [
			'default' => [
				'enable' => true,
				'max' => 1000,
			],
		]
	],
];
