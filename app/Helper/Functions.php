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

function timeToString($time){
	$diff = time()-$time;
	if ($diff < 60){
		return '刚刚';
	}elseif($diff > 60 && $diff <= 60*60){
		return floor($diff/60).'分钟前';
	}elseif($diff > 60*60 && $diff <= 60*60*24){
		return floor($diff / 3600).'小时前';
	}else{
		return date('Y-m-d H:i',$time);
	}
}
function hasForbidWords($content = ''){
	$Setting = new \W7\App\Model\Logic\SettingLogic();
	$words = $Setting->getByKey(\W7\App\Model\Logic\SettingLogic::KEY_FORBID_WORDS,0);
	$words = explode(',',$words);
	$forbid = [];
	foreach ($words as $v){
		if (stripos($content,$v) !== false){
			$forbid[] = $v;
		}
	}
	return $forbid;
}
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	$ckey_length = 4;
	$key         = md5($key);
	$keya        = md5(substr($key, 0, 16));
	$keyb        = md5(substr($key, 16, 16));
	$keyc        = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5($key), -$ckey_length)) : '';

	$cryptkey   = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);

	$string        = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
	$string_length = strlen($string);

	$result = '';
	$box    = range(0, 255);

	$rndkey = array();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for ($j = $i = 0; $i < 256; $i++) {
		$j       = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp     = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a       = ($a + 1) % 256;
		$j       = ($j + $box[$a]) % 256;
		$tmp     = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result  .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}

function generate_label($name, $value, $isFormat = true)
{
	if ($isFormat) {
		$option = [];
		foreach ($value as $key => $val) {
			$option[] = ['label' => $val, 'value' => $key];
		}
	} else {
		$option = $value;
	}
	return ['label' => $name, 'option' => $option];
}

/**
 * 获取当前域名及根路径
 * @return string
 */
function base_url()
{
	static $baseUrl = '';
	if (empty($baseUrl)) {
		$request = \W7\App::getApp()->getContext()->getRequest();
		$baseUrl = $request->getUri()->getScheme() . '://' . $request->server('HTTP_HOST');
	}
	return $baseUrl;
}
