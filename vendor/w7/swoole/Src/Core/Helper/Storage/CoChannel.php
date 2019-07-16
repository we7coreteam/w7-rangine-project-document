<?php
/**
 * 协程通道，用于在协程中数据共享
 * @author donknap
 * @date 18-10-22 上午11:20
 */

namespace W7\Core\Helper\Storage;


use Swoole\Coroutine\Channel;

class CoChannel {
	private $channel = [];

	public function getChannel($name, int $size = 0) {
		if (empty($name)) {
			throw new \RuntimeException('Invalid channel name');
		}

		if (!empty($this->channel[$name])) {
			return $this->channel[$name];
		}

		if ($size > 0) {
			return $this->create($name, $size);
		}

		throw new \RuntimeException('Invalid channel name');
	}

	public function create($name, int $size) {
		$this->channel[$name] = $this->createChannel($size);
		return $this->channel[$name];
	}

	private function createChannel(int $size) {
		if (empty($size) || $size < 0) {
			throw new \RuntimeException('Channel size must be a positive integer greater than one');
		}
		return new Channel($size);
	}
}