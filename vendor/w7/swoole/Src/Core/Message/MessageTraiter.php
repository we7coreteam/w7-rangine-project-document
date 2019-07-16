<?php
/**
 * @author donknap
 * @date 19-1-29 下午7:29
 */

namespace W7\Core\Message;


trait MessageTraiter {
	static public function unpack($data) {
			$dataTmp = unserialize($data);
			if (empty($dataTmp['class'])) {
					throw new \RuntimeException('Invalid message structure');
		}

		$message = new $dataTmp['class']($dataTmp);
		if (empty($dataTmp) || !is_array($dataTmp)) {
					throw new \RuntimeException('Invalid message structure');
		}
		foreach ($dataTmp as $name => $value) {
					$message->$name = $value;
				}
		return $message;
	}
}