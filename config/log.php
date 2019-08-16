<?php
/**
 * 配置日志
 *
 * handler
 *      stack 用于创建「多通道」通道的聚合器
 *      stream
 *      daily 基于 stream
 *      syslog
 *      errorlog
 *      nativemailer 利用php mail()函数发送邮件
 * level
 *      debug
 *      info
 *      notice
 *      warning
 *      error
 *      critical
 *      alert
 *      emergency
 */

return [
	'default' => 'stack',

	'channel' => [
		'stack' => [
			'driver' => 'stack',
			'channel' => ['single'],
		],
		'single' => [
			'driver' => 'daily',
			'path' => RUNTIME_PATH . DS. 'logs'. DS. 'w7.log',
			'level' => ienv('LOG_CHANNEL_SINGLE_LEVEL', 'debug'),
			'days' => '1',
		],
		'database' => [
			'driver' => 'stream',
			'path' => RUNTIME_PATH . DS. 'logs'. DS. 'db.log',
			'level' => ienv('LOG_CHANNEL_DATABASE_LEVEL', 'debug'),
		],
		'invoice' => [
			'driver' => 'daily',
			'path' => RUNTIME_PATH . DS. 'logs'. DS. 'invoice.log',
			'level' => ienv('LOG_CHANNEL_INVOICE_LEVEL', 'debug'),
			'days' => 1,
		]
	],
];
