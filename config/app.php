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
		//SETTING_DEVELOPMENT = DEVELOPMENT^CLEAR_LOG
		//SETTING_DEVELOPMENT = DEBUG|CLEAR_LOG
		//SETTING_DEVELOPMENT = RELEASE|CLEAR_LOG
		'env' => ienv('SETTING_DEVELOPMENT', RELEASE),
		//最新版可用
		'error_level' => E_ALL ^ E_NOTICE,
		'basedir' => [
			'/home/wwwroot/we7/swoole',
			'/tmp',
			sys_get_temp_dir(),
			BASE_PATH,
			APP_PATH,
			RUNTIME_PATH,
			RUNTIME_PATH . DIRECTORY_SEPARATOR . 'logs',
			RUNTIME_PATH . DIRECTORY_SEPARATOR . 'task',
			RUNTIME_PATH . DIRECTORY_SEPARATOR . 'upload',
			BASE_PATH  . DIRECTORY_SEPARATOR . 'vendor',
		]
	],
	'crontab' => [
		'enabled' => false,
		'interval' => 10,
	],
	'reload' => [
		'interval' => ienv('SETTING_RELOAD_INTERVAL', 5), //重复检测的间隔时长
		'debug' => ienv('SETTING_RELOAD_DEBUG', false), //开启后，将不监控文件变化，重复reload，方便调试
	],

	'cache_driver' => ienv('CACHE_DRIVER', 'redis'),
	'cache' => [
		'default' => [
			'driver' => ienv('CACHE_DEFAULT_DRIVER', 'redis'),
			'host' => ienv('CACHE_DEFAULT_HOST', 'redis'),
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
			'charset' => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix' => ienv('DATABASE_DEFAULT_PREFIX', 'ims_'),
			'port' =>ienv('DATABASE_DEFAULT_PORT', 3306),
		],
	],

	'session' => [
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
			'redis' => [
				'enable' => false,
				'max' => 20,
			],
		]
	],
];
