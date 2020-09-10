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
	'default' => 'stack',

	'channel' => [
		'stack' => [
			'driver' => 'stack',
			'channel' => ['single'],
		],
		'single' => [
			'driver' => 'daily',
			'path' => RUNTIME_PATH . DS . 'logs' . DS . 'w7.log',
			'level' => ienv('LOG_CHANNEL_SINGLE_LEVEL', 'debug'),
			'days' => '1',
		],
		'test' => [
			'driver' => 'daily',
			'path' => RUNTIME_PATH . DS . 'logs' . DS . 'test.log',
			'level' => 'debug',
			'days' => 7,
		],
		'error' => [
			'driver' => 'daily',
			'path' => RUNTIME_PATH . DS . 'logs' . DS . 'error.log',
			'level' => 'debug',
			'days' => 7,
		]
	],
];
