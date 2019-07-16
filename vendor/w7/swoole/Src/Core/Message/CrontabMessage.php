<?php
/**
 * @author donknap
 * @date 18-11-26 下午7:47
 */

namespace W7\Core\Message;


/**
 * 计划任务消息包
 */
class CrontabMessage extends MessageAbstract {
	public $messageType = Message::MESSAGE_TYPE_CRONTAB;

	public $name = '';
}