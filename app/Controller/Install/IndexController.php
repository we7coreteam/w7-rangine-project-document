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
use W7\App\Model\Logic\Install\InstallLogic;
use W7\Http\Message\Server\Request;

class IndexController extends BaseController
{
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
		$data = [
			['name' => '已有安装记录', 'result' => file_exists(RUNTIME_PATH . '/install.lock') ? '文档系统已经安装，如果需要重新安装请手动删除 runtime/install.lock 文件' : '未安装', 'enable' => file_exists(RUNTIME_PATH . '/install.lock') ? true : false],
			['name' => '服务器操作系统', 'result' => php_uname(), 'enable' => true],
			['name' => 'PHP版本', 'result' => PHP_VERSION >= 7.2 ? PHP_VERSION : 'PHP版本7.2及以上', 'enable' => PHP_VERSION >= 7.2 ? true : false],
			['name' => '安装程序目录可写', 'result' => is_writable(BASE_PATH) ? BASE_PATH : BASE_PATH . '不可写', 'enable' => is_writable(BASE_PATH) ? true : false],
			['name' => '安装程序运行目录可写', 'result' => is_writable(RUNTIME_PATH) ? RUNTIME_PATH : '不可写', 'enable' => is_writable(RUNTIME_PATH) ? true : false],
			['name' => 'swoole扩展', 'result' => (extension_loaded('swoole') & swoole_version() >= '4.3.0') ? 'swoole版本' . swoole_version() : 'swoole版本4.3.0及以上', 'enable' => (extension_loaded('swoole') & swoole_version() >= '4.3.0') ? true : false],
			['name' => 'mysql扩展', 'result' => extension_loaded('pdo_mysql') ? '已安装pdo_mysql扩展' : '未安装pdo_mysql扩展', 'enable' => extension_loaded('pdo_mysql') ? true : false],
			['name' => 'mbstring扩展', 'result' => extension_loaded('mbstring') ? '已安装mbstring扩展' : '未安装mbstring扩展', 'enable' => extension_loaded('mbstring') ? true : false],
			['name' => 'exec命令', 'result' => function_exists('exec') ? '支持' : '不支持', 'enable' => function_exists('exec') ? true : false],
			['name' => '磁盘空间', 'result' => ($diskfreespace > 200000000) ? $diskfreespaceG . 'G' : '存储空间200M以上', 'enable' => ($diskfreespace > 200000000) ? true : false],
		];
		return $this->data($data);
	}

	/**
	 * @api {post} /install/install 系统安装
	 * @apiName install
	 * @apiGroup install
	 *
	 * @apiParam {String} api_host 服务器地址
	 * @apiParam {Number} server_port 服务器端口号
	 * @apiParam {String} db_database 数据库名称
	 * @apiParam {String} db_host 数据库地址
	 * @apiParam {Number} db_port 数据库端口
	 * @apiParam {String} db_username 数据库用户名
	 * @apiParam {String} db_password 数据库密码
	 * @apiParam {String} db_prefix 数据库表前缀
	 * @apiParam {String} admin_username 管理员账户
	 * @apiParam {String} admin_password 管理员密码
	 * @apiParam {String} cache_driver 缓存驱动 选项：default、redis
	 * @apiParam {String} cache_host 缓存服务器地址（redis时填写）
	 * @apiParam {String} cache_port 缓存服务器端口号（redis时填写）
	 *
	 * @apiSuccessExample {json} Success-Response:
	 * {"status":true,"code":200,"data":"安装已完成！提示：请按照文档配置，重启相关服务","message":"ok"}
	 */
	public function install(Request $request)
	{
		$params = $this->validate($request, [
			'api_host' => 'required',
			'server_port' => 'required',
			'db_database' => 'required',
			'db_host' => 'required',
			'db_port' => 'required',
			'db_username' => 'required',
			'db_password' => 'required',
			'db_prefix' => 'required',
			'admin_username' => 'required',
			'admin_password' => 'required',
			'cache_driver' => 'string',
			'cache_host' => 'string',
			'cache_port' => 'integer',
		], [
			'api_host' => '服务器地址',
			'server_port' => '服务器端口',
			'db_database' => '数据库',
			'db_host' => '数据库地址',
			'db_port' => '数据库端口',
			'db_username' => '数据库用户名',
			'db_password' => '数据库密码',
			'db_prefix' => '数据库表前缀',
			'admin_username' => '管理员密码',
			'admin_password' => '管理员账户',
			'cache_driver' => '缓存驱动',
			'cache_host' => '缓存服务器',
			'cache_port' => '缓存端口',
		]);
		$installLogic = new InstallLogic();
		$data = $installLogic->install($params);
		return $this->data($data);
	}
}
