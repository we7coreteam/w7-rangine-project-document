<?php
/**
 * @author donknap
 * @date 18-10-10 下午5:14
 */

namespace W7\Core\Helper\Communication;


use Swoole\Coroutine\Http\Client;

class ClientCo extends Client {
	private $urls;

	public function __construct($url) {
		$this->urls = $urls = parse_url($url);
		if ($urls['scheme'] == 'https' && empty($urls['port'])) {
			$urls['port'] = '443';
		}
		if (!empty($urls['path'])) {
			$this->urls['raw'] = $urls['path'] . '?' . $urls['query'];
		}
		return parent::__construct($urls['host'], empty($urls['port']) ? '80' : $urls['port'], $urls['scheme'] == 'https' ? true : false);
	}

	public function request($data) {

		return $this->post($this->urls['raw'], $data);
	}

	/**
	 * @param string $path 当path为空时，自动采用构造时传入的值，如果松造时传入的是HOST不是URL，则报错
	 * @param mixed $data
	 */
	public function post($path, $data) {
		if (empty($path)) {
			$path = $this->urls['raw'];
		}

		if (empty($path)) {
			$path = '/';
		}
		return parent::post($path, $data);
	}

	private function setDefaultHeader() {
		$this->setHeaders([
			'Host' => $this->urls['host'],
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
		]);
		return $this;
	}
}