<?php

namespace W7\Console\Command;

use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Fluent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use W7\App;
use W7\Console\Io\Output;
use W7\Core\Database\Connection\PdoMysqlConnection;
use W7\Core\Database\Connection\SwooleMySqlConnection;
use W7\Core\Database\ConnectorManager;
use W7\Core\Database\DatabaseManager;

abstract class CommandAbstract extends Command {
	protected $description;
	/**
	 * @var InputInterface
	 */
	protected $input;
	/**
	 * @var Output
	 */
	protected $output;
	static $isRegister;

	public function __construct(string $name = null) {
		parent::__construct($name);
		$this->setDescription($this->description);
		$this->registerDb();
	}

	/**
	 * model -> newQuery -> DatabaseMananger -> function connection ->
	 *      Factory -> createConnector 拿到一个Pdo连接 （ConnectorManager -> 从连接池里拿一个Pdo连接） -> createConnection 放置Pdo连接，生成连接操作对象 (PdoMysqlConnection)
	 *
	 * @return bool
	 */
	private function registerDb() {
		if (static::$isRegister) {
			return true;
		}

		//新增swoole连接mysql的方式
		Connection::resolverFor('swoolemysql', function ($connection, $database, $prefix, $config) {
			return new SwooleMySqlConnection($connection, $database, $prefix, $config);
		});
		Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
			return new PdoMysqlConnection($connection, $database, $prefix, $config);
		});

		$container = iloader()->withClass(Container::class)->withSingle()->get();
		$container->instance('db.connector.swoolemysql', new ConnectorManager());
		$container->instance('db.connector.mysql', new ConnectorManager());

		//侦听sql执行完后的事件，回收$connection
		$dbDispatch = iloader()->withClass(Dispatcher::class)->withSingle()->withParams('container', $container)->get();
		$dbDispatch->listen(QueryExecuted::class, function ($data) use ($container) {
			/**
			 *检测是否是事物里面的query
			 */
			if (App::getApp()->getContext()->getContextDataByKey('db-transaction')) {
				return false;
			}
			return $this->releaseDb($data, $container);
		});
		$dbDispatch->listen(TransactionBeginning::class, function ($data) {
			$connection = $data->connection;
			App::getApp()->getContext()->setContextDataByKey('db-transaction', $connection);
		});
		$dbDispatch->listen(TransactionCommitted::class, function ($data) use ($container) {
			if (idb()->transactionLevel() === 0) {
				App::getApp()->getContext()->setContextDataByKey('db-transaction', null);
				return $this->releaseDb($data, $container);
			}
		});
		$dbDispatch->listen(TransactionRolledBack::class, function ($data) use ($container) {
			if (idb()->transactionLevel() === 0) {
				App::getApp()->getContext()->setContextDataByKey('db-transaction', null);
				return $this->releaseDb($data, $container);
			}
		});

		$container->instance('events', $dbDispatch);

		//添加配置信息到容器
		$dbconfig = \iconfig()->getUserAppConfig('database');

		$container->instance('config', new Fluent());
		$container['config']['database.default'] = 'default';
		$container['config']['database.connections'] = $dbconfig;
		$factory = new ConnectionFactory($container);
		$dbManager = new DatabaseManager($container, $factory);

		Model::setEventDispatcher($dbDispatch);
		Model::setConnectionResolver($dbManager);

		static::$isRegister = true;
	}

	private function releaseDb($data, $container) {
		$connection = $data->connection;
		ilogger()->channel('database')->debug(($data->sql ?? '') . ', params: ' . implode(',', (array) (empty($data->bindings) ? [] : $data->bindings )));

		$poolName = $connection->getPoolName();
		if (empty($poolName)) {
			return true;
		}
		list($poolType, $poolName) = explode(':', $poolName);
		if (empty($poolType)) {
			$poolType = 'swoolemysql';
		}

		$activePdo = $connection->getActiveConnection();
		if (empty($activePdo)) {
			return false;
		}
		$connectorManager = $container->make('db.connector.' . $poolType);
		$pool = $connectorManager->getCreatedPool($poolName);
		if (empty($pool)) {
			return true;
		}
		$pool->releaseConnection($activePdo);
		return true;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->getApplication()->setDefaultCommand($this->getName());
		$this->input = $input;
		$this->output = $output;

		$this->handle($this->input->getOptions());
	}

	abstract protected function handle($options);

	protected function call($command, $arguments = []) {
		$arguments['command'] = $command;
		$input = new ArrayInput($arguments);
		return $this->getApplication()->find($command)->run(
			$input, ioutputer()
		);
	}
}
