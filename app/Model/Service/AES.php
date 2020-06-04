<?php


namespace W7\App\Model\Service;


class AES
{
	/**
	 * @desc：php aes加密解密类
	 * @author gl
	 * @date 2019/08/31
	 */
	protected $cipher = 'aes-128-ecb';
	/**
	 * CI_Aes key
	 *
	 * @var string
	 */
	protected $key;
	/**
	 * CI_Aes constructor
	 * @param string $key Configuration parameter
	 */

	public function __construct($key="w7document"){
		$this->key = $key;
	}

	/**
	 * Initialize
	 *
	 * @param array $params Configuration parameters
	 * @return CI_Encryption
	 */
	public function initialize($params)
	{
		if (!empty($params) && is_array($params)) {
			foreach ($params as $key => $val) {
				$this->$key = $val;
			}
		}
	}
	/**
	 * Encrypt
	 *
	 * @param string $data Input data
	 * @return string
	 */
	public function encrypt($data) {
		$endata =  openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA);
		return  bin2hex($endata);
	}

	/**
	 * Decrypt
	 *
	 * @param string $data Encrypted data
	 * @return string
	 */
	public function decrypt($data) {
		if(!ctype_alnum($data)){
			return false;
		}
		if(strlen($data)%2!=0){
			return false;
		}
		$encrypted = hex2bin($data);
		return openssl_decrypt($encrypted, $this->cipher, $this->key, OPENSSL_RAW_DATA);
	}
}
