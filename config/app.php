<?php

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
		'addons' => [ //可定义多个通道
			'driver' => 'redis',
			'host' => '',
			'port' => '6379',
			'timeout' => 30,
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
			'prefix' => 'ims_',
			'port' =>'3306',
		],
		'addons' => [
			'driver' => 'mysql',
			'read' => [
				'host' => ['192.168.11.200'],
			],
			'write' => [
				'host' => '192.168.11.200'
			],
			'database' => 'document',
			'username' => 'root',
			'password' => '123456',
			'charset' => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix' => '',
			'port'=>'3306',
		],
	],
	'pool' => [
		'database' => [
			'default' => [
				'enable' => true,
				'max' => 1000,
			],
			'addons' => [
				'enable' => false,
				'max' => 20,
			],
		],
		'cache' => [
			'redis' => [
				'enable' => false,
				'max' => 20,
			],
		]
	],
	'process' => [
		'encrypt' => [
			'enable' => ienv('PROCESS_ENCRYPT_ENABLE', false),
			'class' => \W7\App\Process\EncryptProcess::class,
			'number' => ienv('PROCESS_ENCRYPT_NUMBER', 1),
		]
	],
];
