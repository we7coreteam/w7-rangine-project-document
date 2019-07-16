<?php
/**
 * @author donknap
 * @date 19-3-4 下午6:09
 */

namespace W7\Tcp\Listener;

use W7\App;
use Swoole\Coroutine;
use Swoole\Server;
use W7\Core\Config\Event;
use W7\Core\Listener\ListenerAbstract;
use W7\Tcp\Protocol\Dispatcher;

class ReceiveListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		* @var Server $server
		*/
		list($server, $fd, $reactorId, $data) = $params;

		$this->dispatch($server, $reactorId, $fd, $data);
	}

	/**
	 * 根据用户选择的protocol，把data传到对应protocol的dispatcher
     * @param server $server
     * @param reactorId $reactorId
     * @param fd $fd
     * @param data $data
     */
	private function dispatch(Server $server, $reactorId, $fd, $data) {
		ievent(Event::ON_USER_BEFORE_REQUEST);

		$context = App::getApp()->getContext();
		$context->setContextDataByKey('reactorid', $reactorId);
		$context->setContextDataByKey('workid', $server->worker_id);
		$context->setContextDataByKey('coid', Coroutine::getuid());

		$serverConf = iconfig()->getServer();
		$serverConf = $serverConf[App::$server->type];
		$protocol = $serverConf['protocol'] ?? '';
		Dispatcher::dispatch($protocol, $server, $fd, $data);

		ievent(Event::ON_USER_AFTER_REQUEST);

		$context->destroy();
	}
}