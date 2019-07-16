<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/11
 * Time: 19:11
 */

namespace W7\Laravel\CacheModel\Tests;


use function foo\func;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;

class TestCase extends \PHPUnit\Framework\TestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$container = $this->container();
		$this->db($container);
		
		ini_set('date.timezone', 'Asia/Shanghai');
	}
	
	protected function container()
	{
		$container = Container::getInstance();
		Facade::setFacadeApplication($container);
		$config = [
			'app' => [
				'local_test' => false,
			],
			
			'services' => [
				'uc' => [
					 //'key'   => 'HSIjQgVTWpcia9Mz1iDdMdHHijomUeUPOny3EnX4fSrgE5WBTbQLO01A1gec2i',
                    'key'   => 'ddejcV9K0Bbbdrdi8e6R6ueY6n8r0Tbn5g9J9g4c1B5m8mcZ0Ndc4Zfp4p51f94f',
					 'api'   => 'http://bbs.w7.cc/uc_server',
//					'key'   => 'ddejcV9K0Bbbdrdi8e6R6ueY6n8r0Tbn5g9J9g4c1B5m8mcZ0Ndc4Zfp4p51f94f',
//					'api'   => 'http://127.0.0.1:8888/uc_server',
					'appid' => 2,
				],
			],
			
			'cache' => [
				'default' => env('CACHE_DRIVER', 'redis'),
				'stores'  => [
					'redis' => [
						'driver'     => 'redis',
						'connection' => 'default',
					],
				],
			],
			
			'database' => [
				'redis' => [
					'client'  => 'predis',
					'default' => [
						'host'               => env('REDIS_HOST', '127.0.0.1'),
						'password'           => env('REDIS_PASSWORD', null),
						'port'               => env('REDIS_PORT', 6379),
						'database'           => 0,
						'read_write_timeout' => 0,//new
						'persistent'         => 1,
					],
				],
			],
		];
		
		$container['config']            = new Repository($config);
		$container['config']['app.env'] = 'testing';
		
		$container['events'] = new Dispatcher();
		
		return $container;
	}
	
	protected function db(Container $container)
	{
		$manager = new Manager($container);
		$manager->setAsGlobal();
		
		// 本地数据库
		$manager->addConnection([
			'driver'    => 'mysql',
			'host'      => '172.16.1.13',
			'database'  => 'we7_addons',
			'username'  => 'root',
			'password'  => '123456',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'ims_',
		]);
		
		$manager->addConnection([
			'driver'    => 'mysql',
			'host'      => '172.16.1.13',
			'database'  => 'we7_addons',
			'username'  => 'root',
			'password'  => '123456',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'ims_',
		], '13');
		
		$manager->addConnection([
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'we7_prox',
			'username'  => 'root',
			'password'  => 'root',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'ims_',
		], 'local');
		
		$manager->addConnection([
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'we7_prox',
			'username'  => 'root',
			'password'  => 'root',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'ims_',
		], 'prox');
		
		$manager->addConnection([
			'driver'    => 'mysql',
			'host'      => '172.16.1.13',
			'database'  => 'we7_discuz_gbk',
			'username'  => 'root',
			'password'  => '123456',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'pre_',
		
		], 'bbs');
		$manager->bootEloquent();
		
		$container['app'] = $container;
		$container->singleton('db', function () use ($manager) {
			return $manager->getDatabaseManager();
		});
		
		$container->singleton('cache', function ($app) {
			return new CacheManager($app);
		});
		
		$container->singleton('cache.store', function ($app) {
			return $app['cache']->driver();
		});
		
		$container->singleton('redis', function ($app) {
			$config = $app->make('config')->get('database.redis');
			
			return new RedisManager(Arr::pull($config, 'client', 'predis'), $config);
		});
		
		$container->bind('redis.connection', function ($app) {
			return $app['redis']->connection();
		});
		
		// $event = new Dispatcher($container);
		// $manager->setEventDispatcher($event);
		
		Manager::listen(function ($query, $bindings = null, $time = null, $connectionName = null) {
			if ($query instanceof \Illuminate\Database\Events\QueryExecuted) {
				$bindings   = $query->bindings;
				$time       = $query->time;
				$connection = $query->connection;
				$query      = $query->sql;
				echo $query . PHP_EOL;
			}
		});
		
	}
}