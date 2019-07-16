<?php
/**
 * @author donknap
 * @date 18-7-25 下午4:53
 */

namespace W7\Core\Listener;

interface ListenerInterface {
	/**
	 * 这里定义接口不能包含参数列表，子类中需要通过
	 */
	public function run(...$params);
}
