<?php

namespace W7\Core\Crontab;

use Exception;
use InvalidArgumentException;

class CrontabTask{
	private $name;
	private $config;
	private $date;

	public function __construct($name, $config) {
		$this->name = $name;
		$this->config = $config;
		$this->parse();
	}

	public function getName() {
		return $this->name;
	}

	public function getTask() {
		return $this->config['task'];
	}

	public function getRule() {
		return $this->config['rule'];
	}

	/**
     *  解析crontab的定时格式，linux只支持到分钟/，这个类支持到秒
     * @param string $crontab_string :
     *
     *      0     1    2    3    4    5
     *      *     *    *    *    *    *
     *      -     -    -    -    -    -
     *      |     |    |    |    |    |
     *      |     |    |    |    |    +----- day of week (0 - 6) (Sunday=0)
     *      |     |    |    |    +----- month (1 - 12)
     *      |     |    |    +------- day of month (1 - 31)
     *      |     |    +--------- hour (0 - 23)
     *      |     +----------- min (0 - 59)
     *      +------------- sec (0-59)
     * @param int $start_time timestamp [default=current timestamp]
     * @return int unix timestamp - 下一分钟内执行是否需要执行任务，如果需要，则把需要在那几秒执行返回
     * @throws InvalidArgumentException 错误信息
     */
	private function parse(){
		$rule = $this->getRule();
		if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i',
			trim($rule))
		) {
			if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i',
				trim($rule))
			) {
				 throw new Exception("Invalid cron string: " . $rule);
			}
		}
		$cron = preg_split("/[\s]+/i", trim($rule));

		if (count($cron) == 6) {
			$date = array(
				'second' => (empty($cron[0])) ? array(1 => 1) : $this->parseCronNumber($cron[0], 1, 59),
				'minutes' => $this->parseCronNumber($cron[1], 0, 59),
				'hours' => $this->parseCronNumber($cron[2], 0, 23),
				'day' => $this->parseCronNumber($cron[3], 1, 31),
				'month' => $this->parseCronNumber($cron[4], 1, 12),
				'week' => $this->parseCronNumber($cron[5], 0, 6),
			);
		} elseif (count($cron) == 5) {
			$date = array(
				'second' => array(1 => 1),
				'minutes' => $this->parseCronNumber($cron[0], 0, 59),
				'hours' => $this->parseCronNumber($cron[1], 0, 23),
				'day' => $this->parseCronNumber($cron[2], 1, 31),
				'month' => $this->parseCronNumber($cron[3], 1, 12),
				'week' => $this->parseCronNumber($cron[4], 0, 6),
			);
		} else {
			throw new Exception("Invalid cron string: " . $rule);
		}

		$this->date = $date;
	}

	/**
	 * 解析单个配置的含义
	 * @param $s
	 * @param $min
	 * @param $max
	 * @return array
	 */
	protected function parseCronNumber($s, $min, $max){
		$result = array();
		$v1 = explode(",", $s);
		foreach ($v1 as $v2) {
			$v3 = explode("/", $v2);
			$step = empty($v3[1]) ? 1 : $v3[1];
			$v4 = explode("-", $v3[0]);
			$_min = count($v4) == 2 ? $v4[0] : ($v3[0] == "*" ? $min : $v3[0]);
			$_max = count($v4) == 2 ? $v4[1] : ($v3[0] == "*" ? $max : $v3[0]);
			for ($i = $_min; $i <= $_max; $i += $step) {
				if (intval($i) < $min) {
					$result[$min] = $min;
				} elseif (intval($i) > $max) {
					$result[$max] = $max;
				} else {
					$result[$i] = intval($i);
				}
			}
		}
		ksort($result);
		return $result;
	}

	public function check($time) {
		if (
			in_array(intval(date('s', $time)), $this->date['second']) &&
			in_array(intval(date('i', $time)), $this->date['minutes']) &&
			in_array(intval(date('G', $time)), $this->date['hours']) &&
			in_array(intval(date('j', $time)), $this->date['day']) &&
			in_array(intval(date('w', $time)), $this->date['week']) &&
			in_array(intval(date('n', $time)), $this->date['month'])
		) {
			return true;
		}

		return false;
	}
}