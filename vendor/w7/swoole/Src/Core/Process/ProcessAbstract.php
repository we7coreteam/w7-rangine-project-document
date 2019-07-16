<?php
/**
 * @author donknap
 * @date 18-11-22 下午8:27
 */

namespace W7\Core\Process;

/**
 * 定义此函数后，将自动添加事件循环，用于接收主进程通过管道传递的数据
 * return false 或取消循环
 * 也可以在函数中调用 swoole_event_del 自行取消
 * 如果在函数内调用 $process->exit() 每次接收完后后，会销毁进程，以待下次重建
 * 如果不启用该函数，需要在run函数内，while (1) 来侦听读取数据
 * @method boolean read(\Swoole\Process $process, $data)
 * @package W7\Core\Process
 */
abstract class ProcessAbstract implements ProcessInterface {
	/**
	 * 设置读取间隔时间，默认是1秒，如果有需要可以类中自定义
	 * @var int
	 */
	public $readInterval = 1;
}