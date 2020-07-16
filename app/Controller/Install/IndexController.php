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

namespace W7\App\Controller\Install;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\Install\InstallLogic;
use W7\Http\Message\Server\Request;

class IndexController extends BaseController
{
	public function config(Request $request)
	{
		$isInstall = file_exists(RUNTIME_PATH . '/install.lock');
		$data = [
			'is_install' => $isInstall,
			'api_host' => ienv('API_HOST'),
			'db_username' => ienv('DATABASE_DEFAULT_DATABASE'),
		];
		return $this->data($data);
	}

	/**
	 * @api {post} /install/systemDetection 系统检测
	 * @apiName systemDetection
	 * @apiGroup install
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":{"composer_install":{"name":"composer install","result":"已执行","enable":true},"system":{"name":"服务器操作系统","result":"Linux 9c290f2b8a1a 4.19.76-linuxkit #1 SMP Fri Apr 3 15:53:26 UTC 2020 x86_64","enable":true},"php_version":{"name":"PHP版本","result":"7.2.24","enable":true},"base_path":{"name":"安装程序目录可写","result":"\/home\/wwwroot\/doc","enable":true},"runtime_path":{"name":"安装程序运行目录可写","result":"\/home\/wwwroot\/doc\/runtime","enable":true},"swoole":{"name":"swoole扩展","result":"swoole版本4.4.4","enable":true},"pdo_mysql":{"name":"mysql扩展","result":"已安装pdo_mysql扩展","enable":true},"mbstring":{"name":"mbstring扩展","result":"已安装mbstring扩展","enable":true},"diskfreespace":{"name":"磁盘空间","result":"36.78G","enable":true}},"message":"ok"}
	 */
	public function systemDetection(Request $request)
	{
		$diskfreespace = diskfreespace(BASE_PATH);
		$diskfreespaceG = (ceil($diskfreespace / 1000 / 1000 / 10) / 100);
		$isInstall = file_exists(RUNTIME_PATH . '/install.lock');
		if ($isInstall) {
			$data = [
				['id' => 1, 'name' => '已有安装记录', 'result' => $isInstall ? '文档系统已经安装，如果需要重新安装请手动删除 runtime/install.lock 文件' : '未安装', 'enable' => $isInstall ? true : false],
			];
		} else {
			$data = [
				['id' => 1, 'name' => '已有安装记录', 'result' => $isInstall ? '文档系统已经安装，如果需要重新安装请手动删除 runtime/install.lock 文件' : '未安装', 'enable' => $isInstall ? true : false],
				['id' => 2, 'name' => '服务器操作系统', 'result' => php_uname(), 'enable' => true],
				['id' => 3, 'name' => 'PHP版本', 'result' => PHP_VERSION >= 7.2 ? PHP_VERSION : 'PHP版本7.2及以上', 'enable' => PHP_VERSION >= 7.2 ? true : false],
				['id' => 4, 'name' => '安装程序目录可写', 'result' => is_writable(BASE_PATH) ? BASE_PATH : BASE_PATH . '不可写', 'enable' => is_writable(BASE_PATH) ? true : false],
				['id' => 5, 'name' => '安装程序运行目录可写', 'result' => is_writable(RUNTIME_PATH) ? RUNTIME_PATH : '不可写', 'enable' => is_writable(RUNTIME_PATH) ? true : false],
				['id' => 6, 'name' => 'swoole扩展', 'result' => (extension_loaded('swoole') & swoole_version() >= '4.3.0') ? 'swoole版本' . swoole_version() : 'swoole版本4.3.0及以上', 'enable' => (extension_loaded('swoole') & swoole_version() >= '4.3.0') ? true : false],
				['id' => 7, 'name' => 'mysql扩展', 'result' => extension_loaded('pdo_mysql') ? '已安装pdo_mysql扩展' : '未安装pdo_mysql扩展', 'enable' => extension_loaded('pdo_mysql') ? true : false],
				['id' => 8, 'name' => 'mbstring扩展', 'result' => extension_loaded('mbstring') ? '已安装mbstring扩展' : '未安装mbstring扩展', 'enable' => extension_loaded('mbstring') ? true : false],
				['id' => 9, 'name' => 'exec命令', 'result' => function_exists('exec') ? '支持' : '不支持', 'enable' => function_exists('exec') ? true : false],
				['id' => 10, 'name' => '磁盘空间', 'result' => ($diskfreespace > 200000000) ? $diskfreespaceG . 'G' : '存储空间200M以上', 'enable' => ($diskfreespace > 200000000) ? true : false],
				['id' => 11, 'name' => 'redis扩展', 'result' => extension_loaded('redis') ? '已安装redis扩展' : '未安装redis扩展', 'enable' => extension_loaded('redis') ? true : false],
			];
		}
		return $this->data($data);
	}

	/**
	 * @api {post} /install/install 系统安装
	 * @apiName install
	 * @apiGroup install
	 *
	 * @apiParam {String} api_host 服务器地址
	 * @apiParam {String} db_database 数据库名称
	 * @apiParam {String} db_host 数据库地址
	 * @apiParam {String} db_username 数据库用户名
	 * @apiParam {String} db_password 数据库密码
	 * @apiParam {String} db_prefix 数据库表前缀
	 * @apiParam {String} admin_username 管理员账户
	 * @apiParam {String} admin_password 管理员密码
	 * @apiParam {String} cache_driver 缓存驱动 选项：redis
	 * @apiParam {String} cache_host 缓存服务器地址（redis时填写）
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":"安装已完成！提示：请按照文档配置，重启相关服务","message":"ok"}
	 */
	public function install(Request $request)
	{
		$params = $this->validate($request, [
			'api_host' => 'required',
			'db_database' => 'required',
			'db_host' => 'required',
			'db_username' => 'required',
			'db_password' => 'required',
			'db_prefix' => 'required',
			'admin_username' => 'required',
			'admin_password' => 'required',
			'cache_host' => 'string',
		], [
			'api_host' => '服务器地址',
			'db_database' => '数据库',
			'db_host' => '数据库地址',
			'db_username' => '数据库用户名',
			'db_password' => '数据库密码',
			'db_prefix' => '数据库表前缀',
			'admin_username' => '管理员密码',
			'admin_password' => '管理员账户',
			'cache_host' => '缓存服务器',
		]);
		$apiHost = explode(':', $params['api_host']);
		if (count($apiHost) < 3 || (!is_numeric($apiHost[2]))) {
			throw new ErrorHttpException('请填写协议与端口号');
		}
		$params['api_host'] = $apiHost[0] . ':' . $apiHost[1] . '/';
		$params['server_port'] = $apiHost[2];

		$dbHost = explode(':', $params['db_host']);
		if (count($dbHost) < 2 || (!is_numeric($dbHost[1]))) {
			throw new ErrorHttpException('请填写数据库端口号');
		}
		$params['db_host'] = $dbHost[0];
		$params['db_port'] = $dbHost[1];

		$params['cache_driver'] = 'redis';
		$cacheHost = explode(':', $params['cache_host']);
		if (count($cacheHost) < 2 || (!is_numeric($cacheHost[1]))) {
			throw new ErrorHttpException('请填写redis端口号');
		}
		$params['cache_host'] = $cacheHost[0];
		$params['cache_port'] = $cacheHost[1];

		$installLogic = new InstallLogic();
		$data = $installLogic->install($params);
		return $this->data($data);
	}
}
