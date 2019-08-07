<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Model\Service\Api;

class Curl
{
	private $curl;
	protected $timeout = 5;
	protected $baseUrl = '';
	protected $responseType = 'json';

	public function __construct()
	{
		$this->curl = curl_init();
	}

	private function getResponse($response)
	{
		if ($this->responseType == 'json') {
			return json_decode($response, true);
		}

		if ($this->responseType == 'xml') {
			return json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		}

		return $response;
	}

	public function get($url, $params=[], $header=[])
	{
		$params = is_array($params) ? http_build_query($params):$params;
		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->baseUrl.'/'.$url . '?' . $params,
			CURLOPT_TIMEOUT        => $this->timeout,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTPHEADER     => $header,
			//CURLOPT_HEADER => true,
		]);
		$response = curl_exec($this->curl);
		if ($response === false) {
			throw new \Exception('请求发生错误：' . curl_error($this->curl));
		}
		//list($header,$body) = explode("\r\n\r\n",$response);
		return $this->getResponse($response);
	}

	public function post($url, $params=[], $header=[])
	{
		curl_setopt_array($this->curl, [
			CURLOPT_POST => true,
			CURLOPT_URL => $this->baseUrl.'/'.$url,
			CURLOPT_TIMEOUT        => $this->timeout,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTPHEADER     => $header,
			CURLOPT_POSTFIELDS => is_array($params) ? http_build_query($params):$params,
		]);

		$response = curl_exec($this->curl);
		if ($response === false) {
			throw new \Exception('请求发生错误：' . curl_error($this->curl));
		}
		return $this->getResponse($response);
	}

	//form-data
	public function postOrigin($url, array $params=[], $header=[])
	{
		foreach ($params as $k=>$v) {
			if (isset($v['type']) && $v['type'] == 'file') {
				$params[$k] = new \CURLFile(realpath($v['path']));
			}
		}
		curl_setopt_array($this->curl, [
			CURLOPT_SAFE_UPLOAD => true,
			CURLOPT_POST => true,
			CURLOPT_URL => $this->baseUrl.'/'.$url,
			CURLOPT_TIMEOUT        => $this->timeout,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTPHEADER     => $header,
			CURLOPT_POSTFIELDS => $params,
		]);

		$response = curl_exec($this->curl);
		if ($response === false) {
			throw new \Exception('请求发生错误：' . curl_error($this->curl));
		}
		return $this->getResponse($response);
	}

	public function postJson($url, $params=[], $header=[])
	{
		$params = is_array($params) ? json_encode($params, JSON_UNESCAPED_UNICODE):$params;
		$header[] = 'Content-Type: application/json';
		$header[] = 'Content-Length: ' . strlen($params);
		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->baseUrl.'/'.$url,
			CURLOPT_TIMEOUT => $this->timeout,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => $params
		]);
		$response = curl_exec($this->curl);
		if ($response === false) {
			throw new \Exception('请求发生错误：' . curl_error($this->curl));
		}
		return $this->getResponse($response);
	}

	public function __destruct()
	{
		curl_close($this->curl);
	}
}
