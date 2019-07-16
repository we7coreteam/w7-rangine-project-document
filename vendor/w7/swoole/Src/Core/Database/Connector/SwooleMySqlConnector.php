<?php
/**
 * @author donknap
 * @date 18-10-24 下午3:40
 */

namespace W7\Core\Database\Connector;


use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;
use W7\Core\Database\Driver\MySqlCoroutine;

class SwooleMySqlConnector extends Connector implements ConnectorInterface {

	public function connect(array $config) {
		$connection = new MySqlCoroutine();
		$connection->connect([
			'host' => $config['host'],
			'port' => !empty($config['port']) ? $config['port'] : '3306',
			'user' => $config['username'],
			'password' => $config['password'],
			'database' => $config['database'],
			'charset' => $config['charset'],
			'strict_type' => false,
			'fetch_mode' => true,
		]);
		return $connection;
	}
}