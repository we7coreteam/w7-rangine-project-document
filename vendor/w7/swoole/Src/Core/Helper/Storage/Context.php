<?php
/**
 * 存储上下文数据，方便调用
 * @author donknap & Swoft\Core
 * @date 18-7-24 下午3:09
 */
namespace W7\Core\Helper\Storage;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Swoole\Coroutine;
use W7\Http\Message\Server\Request;
use W7\Http\Message\Server\Response;

class Context {
	/**
	 * Key of request context share data
	 */
	const DATA_KEY = 'data';

	/**
	 * Key of current Request
	 */
	const REQUEST_KEY = 'request';

	/**
	 * Key of current Response
	 */
	const RESPONSE_KEY = 'response';

	/**
	 * @var array Coroutine context
	 */
	private static $context;

	/**
	 * 中间件
	 */
	const MIDDLEWARE_KEY = 'lastMiddleware';

	/**
	 * 路由表
	 */
	const ROUTE_KEY = "route";


	const LOG_REQUEST_KEY = "requestlog";

	/**
	 * @return Request|null
	 */
	public function getRequest()
	{
		return self::getCoroutineContext(self::REQUEST_KEY);
	}

	/**
	 * @return Response|null
	 */
	public function getResponse()
	{
		return self::getCoroutineContext(self::RESPONSE_KEY);
	}

	/**
	 * @return array|null
	 */
	public function getContextData()
	{
		return self::getCoroutineContext(self::DATA_KEY);
	}

	/**
	 * Set the object of request
	 *
	 * @param RequestInterface $request
	 */
	public function setRequest(RequestInterface $request)
	{
		$coroutineId = self::getCoroutineId();
		self::$context[$coroutineId][self::REQUEST_KEY] = $request;
	}

	/**
	 * Set the object of response
	 *
	 * @param ResponseInterface $response
	 */
	public function setResponse(ResponseInterface $response)
	{
		$coroutineId = self::getCoroutineId();
		self::$context[$coroutineId][self::RESPONSE_KEY] = $response;
	}

	/**
	 * Set the context data
	 *
	 * @param array $contextData
	 */
	public function setContextData(array $contextData = [])
	{
		$existContext = [];
		$coroutineId = self::getCoroutineId();
		if (isset(self::$context[$coroutineId][self::DATA_KEY])) {
			$existContext = self::$context[$coroutineId][self::DATA_KEY];
		}
		self::$context[$coroutineId][self::DATA_KEY] = array_merge([], $contextData, $existContext);
	}

	/**
	 * Update context data by key
	 *
	 * @param string $key
	 * @param mixed $val
	 */
	public function setContextDataByKey(string $key, $val)
	{
		$coroutineId = self::getCoroutineId();
		self::$context[$coroutineId][self::DATA_KEY][$key] = $val;
	}

	/**
	 * Get Current Request Log ID
	 *
	 * @return string
	 */
	public function getLogid(): string
	{
		$contextData = self::getCoroutineContext(static::LOG_REQUEST_KEY);
		$logid = $contextData['logid'] ?? '';
		return $logid;
	}

	/**
	 * Get Current Request Span ID
	 *
	 * @return int
	 */
	public function getSpanid(): int
	{
		$contextData = self::getCoroutineContext(static::LOG_REQUEST_KEY);

		return $contextData['spanid'] ? (int)$contextData['spanid'] : 0;
	}


	/**
	 * Get context data by key
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getContextDataByKey(string $key, $default = null)
	{
		$coroutineId = self::getCoroutineId();
		if (isset(self::$context[$coroutineId][self::DATA_KEY][$key])) {
			return self::$context[$coroutineId][self::DATA_KEY][$key];
		}

		return $default;
	}

	/**
	 * Destroy all current coroutine context data
	 */
	public function destroy()
	{
		$coroutineId = self::getCoroutineId();
		if (isset(self::$context[$coroutineId])) {
			unset(self::$context[$coroutineId]);
		}
	}

	/**
	 * Get data from coroutine context by key
	 *
	 * @param string $key key of context
	 * @return mixed|null
	 */
	private function getCoroutineContext(string $key)
	{
		$coroutineId = self::getCoroutineId();
		if (!isset(self::$context[$coroutineId])) {
			return null;
		}

		$coroutineContext = self::$context[$coroutineId];
		if (isset($coroutineContext[$key])) {
			return $coroutineContext[$key];
		}
		return null;
	}

	/**
	 * Get current coroutine ID
	 *
	 * @return int|null Return null when in non-coroutine context
	 */
	private function getCoroutineId()
	{
		return Coroutine::getuid();
	}
}
