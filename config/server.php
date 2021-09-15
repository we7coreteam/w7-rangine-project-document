<?php
/**
 * @author donknap
 * @date 18-7-18 下午5:41
 */

$serverSetting = [
	'common' => [
		'pname' => 'document_open_source',
		'pid_file' => '/tmp/swoole.pid',
		'max_request' => 10000,
		'worker_num' => ienv('SERVER_COMMON_WORKER_NUM', 2),
		'task_worker_num' => ienv('SERVER_COMMON_TASK_WORKER_NUM', 1),
		'package_max_length' => ienv('SERVER_COMMON_PACKAGE_MAX_LENGTH', 5242880), // 5M
		'buffer_output_size' => ienv('SERVER_COMMON_BUFFER_MAX_LENGTH', 10485760) // 10*1024*1024
	],
	'tcp' => [
		'host' => '0.0.0.0',
		'port' => ienv('SERVER_TCP_PORT', '9999')
	],
	'http' => [
		'host' => '0.0.0.0',
		'port' => ienv('SERVER_HTTP_PORT', '99')
	],
];

return $serverSetting;
