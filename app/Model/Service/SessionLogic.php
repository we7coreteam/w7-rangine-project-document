<?php
namespace W7\App\Model\Service;

class SessionLogic extends BaseLogic {
	protected $token;
	protected static $instance;

	public static function createInstance($token)
	{
		if(!self::$instance){
			self::$instance = new self($token);
		}
	}

	public static function getInstance()
	{
		return self::$instance;
	}

	public function __construct($token)
	{
		$this->token = $token;
	}

	public function get($key)
	{
		return cache()::get($this->getKey($key));
	}

	public function set($key,$value)
	{
		cache()::set($this->getKey($key),$value,$this->getTtl());
	}

	public function getKey($key)
	{
		return $this->token.'_'.$key;
	}

	public function getTtl()
	{
		return cache()->getExpireAt($this->token) - time();
	}
}
