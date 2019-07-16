<?php

namespace W7\Core\Exception;

class ExceptionHandle {
	private $exceptionMap = [
		'http' => HttpException::class,
		'http_dev' => HttpDevException::class,
		'http_release' => HttpReleaseException::class,
		'tcp' => TcpException::class,
		'tcp_dev' => TcpException::class,
		'tcp_release' => HttpReleaseException::class
	];
	private $type;
	private $env;

	public function __construct($type) {
		$this->type = $type;
		$this->env = 'release';
		if ((ENV & DEBUG) === DEBUG) {
			$this->env = 'dev';
		}
	}

	public function handle(\Throwable $throwable) {
		if (!($throwable instanceof ResponseException)) {
			$exception = $this->exceptionMap[$this->type . '_' . $this->env];
			$throwable = new $exception($throwable->getMessage(), $throwable->getCode(), $throwable);
		}
		return $throwable->render();
	}

	public function registerException($type, $class) {
		$this->exceptionMap[$type] = $class;
	}
}