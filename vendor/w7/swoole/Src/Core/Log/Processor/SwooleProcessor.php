<?php
/**
 * 扩展monolog日志中包含swoole进程及协程信息
 * @author donknap
 * @date 18-10-20 下午2:58
 */

namespace W7\Core\Log\Processor;


use W7\App;

class SwooleProcessor {
	private $context;

	public function __construct() {
		$this->context = App::getApp()->getContext();
	}

	public function __invoke(array $record) {
		$workid = $this->context->getContextDataByKey('workid');
		$coid = $this->context->getContextDataByKey('coid');

		$record['workid'] = $workid ? $workid : '0';
		$record['coid'] = $coid ? $coid : '0';
		return $record;
	}
}