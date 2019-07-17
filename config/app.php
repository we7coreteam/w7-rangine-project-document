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
			'/home/wwwroot/we7/swoole'
		]
	],
	'crontab' => [
		'enabled' => false,
		'interval' => 10,
	],
	'reload' => [
		'interval' => 5, //重复检测的间隔时长
		'debug' => false, //开启后，将不监控文件变化，重复reload，方便调试
	],
	'cache' => [
		'default' => [
			'driver' => 'redis',
			'host' => 'redis',
			'port' => '6379',
			'timeout' => 30,
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
			'driver' => 'mysql',
			'database' => 'document',
			'host' => '192.168.11.200',
			'username' => 'root',
			'password' => '123456',
			'charset' => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix' => '',
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
