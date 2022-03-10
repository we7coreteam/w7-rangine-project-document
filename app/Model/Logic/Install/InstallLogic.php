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

namespace W7\App\Model\Logic\Install;

use W7\App\Exception\InternalException;
use W7\App\Model\Logic\UserLogic;

class InstallLogic
{
	public function install($config)
	{
		if (ienv('DATABASE_DEFAULT_DATABASE')) {
			//已安装已重启
			throw new InternalException('文档系统已经安装，如果需要重新安装请手动删除 配置 文件');
		}
		try {
			// 是否已安装
			$lockFile = RUNTIME_PATH . '/install.lock';
			if (file_exists($lockFile)) {
				throw new InternalException('文档系统已经安装，如果需要重新安装请手动删除 runtime/install.lock 文件');
			}

			// 版本检查
			$this->checkExtension();

			if($config['cache_driver']== 'redis'){
				//如果缓存是redis服务进行校验，否则不校验
				try {
					$redis = new \Redis();
					$connect = $redis->connect($config['cache_host'], $config['cache_port'], 15);
					if (!$connect) {
						throw new InternalException('redis链接失败');
					}
				} catch (\Throwable $exception) {
					throw new InternalException('redis链接失败：' . $exception->getMessage());
				}
			}

			//仅做验证，不进行安装操作
			if ($config['option'] == 'check') {
				//验证数据库
				try {
					$connect = new \PDO("mysql:host={$config['db_host']};port={$config['db_port']};charset=utf8", $config['db_username'], $config['db_password']);
					if (!$connect) {
						throw new InternalException('数据库链接失败');
					}
					$connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
					$statement = $connect->query("SHOW DATABASES LIKE '{$config['db_database']}';");
					$hasDb = $statement->fetch();
					if ($hasDb) {
						throw new InternalException('数据库已存在');
					}
				} catch (\Throwable $exception) {
					throw new InternalException($exception->getMessage());
				}

				return '验证通过';
			}

			// 生成配置文件
			$this->generateConfig($config);

			// 初始化数据库
			$this->initDatabase($config);

			// 生成lock文件
			file_put_contents($lockFile, 'success');

			return '安装已完成！提示：配置文件重启后生效，请按照文档配置，重启相关服务';
		} catch (\Exception $e) {
			throw new InternalException($e->getMessage());
		}
	}

	private function initDatabase($config)
	{
		// 创建数据库
		try {
			$connect = new \PDO("mysql:host={$config['db_host']};port={$config['db_port']};charset=utf8", $config['db_username'], $config['db_password']);
			$connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$sql = "CREATE DATABASE IF NOT EXISTS {$config['db_database']} DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
			$connect->exec($sql);
			$statement = $connect->query("SHOW DATABASES LIKE '{$config['db_database']}';");
			if (empty($statement->fetch())) {
				throw new InternalException('创建数据库失败！');
			}

			$connect->exec("USE {$config['db_database']};");
			$statement = $connect->query("SHOW TABLES LIKE '{$config['db_prefix']}%';");
			if (!empty($statement->fetch())) {
				throw new InternalException('您的数据库不为空，请重新建立数据库或清空该数据库或更改表前缀！');
			}

			// 导入数据
			$importSql = file_get_contents(BASE_PATH . '/install/document.sql');
			$importSql = str_replace('ims_', $config['db_prefix'], $importSql);
			$connect->exec($importSql);
			$connect = null;

			// 创建系统管理员账号
			$this->createAdmin($config);

			return true;
		} catch (\PDOException $e) {
			throw new InternalException($e->getMessage());
		}
	}

	private function createAdmin($config)
	{
		try {
			$connect = new \PDO("mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_database']};charset=utf8mb4", $config['db_username'], $config['db_password']);
			$username = $config['admin_username'];
			$password = UserLogic::instance()->userPwdEncryption($username, $config['admin_password']);
			$userTable = $config['db_prefix'] . 'user';

			$adminInsert = [
				'username' => $username,
				'userpass' => $password,
				'is_ban' => 0,
				'remark' => '超管',
				'group_id' => 1,
				'created_at' => time(),
				'updated_at' => time(),
			];

			$sql = "INSERT INTO `{$userTable}` (`" . implode('`,`', array_keys($adminInsert)) . "`) VALUE ('" . implode("','", $adminInsert) . "')";
			$connect->exec($sql);
			$statement = $connect->query("SELECT * FROM {$userTable} WHERE username = '{$username}'");
			if (empty($statement->fetch())) {
				throw new InternalException('创建系统管理员失败！');
			}
			$connect = null;
		} catch (\PDOException $e) {
			throw new InternalException($e->getMessage());
		}
	}

	private function generateConfig($config)
	{
		$env = file_get_contents(BASE_PATH . '/install/.env.template');
		$env = str_replace('{{API_HOST}}', $config['api_host'], $env);
		// server
		$env = str_replace('{{SERVER_HTTP_PORT}}', $config['server_port'], $env);
		// db
		$env = str_replace('{{DATABASE_DEFAULT_DATABASE}}', $config['db_database'], $env);
		$env = str_replace('{{DATABASE_DEFAULT_HOST}}', $config['db_host'], $env);
		$env = str_replace('{{DATABASE_DEFAULT_PORT}}', $config['db_port'], $env);
		$env = str_replace('{{DATABASE_DEFAULT_USERNAME}}', $config['db_username'], $env);
		$env = str_replace('{{DATABASE_DEFAULT_PASSWORD}}', $config['db_password'], $env);
		$env = str_replace('{{DATABASE_DEFAULT_PREFIX}}', $config['db_prefix'], $env);
		// cache
		$env = str_replace('{{CACHE_DEFAULT_DRIVER}}', $config['cache_driver'], $env);
		if ($config['cache_driver'] == 'redis') {
			$env = str_replace('{{CACHE_DEFAULT_HOST}}', $config['cache_host'] ?? '', $env);
			$env = str_replace('{{CACHE_DEFAULT_PORT}}', $config['cache_port'] ?? '', $env);
			$env = str_replace('{{CACHE_DEFAULT_PASSWORD}}', '', $env);
		} else {
			$env = str_replace('{{CACHE_DEFAULT_HOST}}', '127.0.0.1', $env);
			$env = str_replace('{{CACHE_DEFAULT_PORT}}', '6379', $env);
			$env = str_replace('{{CACHE_DEFAULT_PASSWORD}}', '', $env);
		}

		if (file_put_contents(BASE_PATH . '/.env', $env) === false) {
			throw new InternalException('配置文件写入失败！');
		}
	}

	private function checkExtension()
	{
		if (version_compare(PHP_VERSION, '7.2.0', '<')) {
			throw new InternalException('PHP 版本必须>= 7.2.0');
		}

		$extension = ['pdo_mysql', 'mbstring', 'swoole'];
		foreach ($extension as $ext) {
			if (!extension_loaded($ext)) {
				throw new InternalException($ext . ' 扩展未安装');
			}
		}

		if (version_compare(swoole_version(), '4.3.0', '<')) {
			throw new InternalException('swoole 版本必须>= 4.3.0');
		}

		if (is_writable(BASE_PATH) === false) {
			throw new InternalException('请保证' . BASE_PATH . '目录有写权限！');
		}

		if (is_writable(RUNTIME_PATH) === false) {
			throw new InternalException('请保证' . RUNTIME_PATH . '目录有写权限！');
		}

		if (!file_exists(BASE_PATH . '/composer.json')) {
			throw new InternalException('请先执行 composer install --no-dev 安装扩展包');
		}
		return true;
	}
}
