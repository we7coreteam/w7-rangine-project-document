<?php
/**
 * @author donknap
 * @date 18-11-26 下午6:56
 */

namespace W7\Core\Message;


class Message {
	use MessageTraiter;

	const MESSAGE_TYPE_TASK = 'task';
	const MESSAGE_TYPE_CRONTAB = 'crontab';
}