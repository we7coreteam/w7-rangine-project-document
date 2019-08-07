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

namespace W7\App\Model\Service;

class EncryptorLogic extends BaseLogic
{
	public static function encrypt($str, $key='whatisup', $salt_number=3)
	{
		if ($salt_number > 32) {
			$salt_number = 32;
		}
		$key = md5($key);
		$salt = md5(rand(0, 100));
		$salt = substr($salt, 0, $salt_number);
		$tmp = '';
		for ($i=0;$i<strlen($str);$i++) {
			$tmp .= substr($str, $i, 1) ^ substr($key, $i%32, 1);
		}

		$arr = str_split(base64_encode($tmp));
		foreach (str_split($salt) as $k=>$v) {
			if ($k < count($arr)) {
				$arr[$k] .= $v;
			}
		}
		$tmp = join('', $arr);
		return str_replace(['=','+','/'], ['o0o0o','o000o','oo00o'], $tmp);
	}

	public static function decrypt($str, $key='whatisup', $salt_number=3)
	{
		if ($salt_number > 32) {
			$salt_number = 32;
		}
		$key = md5($key);
		$arr = str_split(str_replace(['o0o0o','o000o','oo00o'], ['=','+','/'], $str));
		for ($k=0;$k<$salt_number;$k++) {
			if (isset($arr[2*$k+1])) {
				unset($arr[2*$k+1]);
			}
		}
		$xor = base64_decode(join('', $arr));
		$tmp = '';
		for ($i=0;$i<strlen($xor);$i++) {
			$tmp .= substr($xor, $i, 1) ^ substr($key, $i%32, 1);
		}
		return $tmp;
	}
}
