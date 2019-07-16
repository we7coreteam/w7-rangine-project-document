<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/31
 * Time: 10:19
 */

use Illuminate\Container\Container;
use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('app')) {
	/**
	 * Get the available container instance.
	 *
	 * @param  string $abstract
	 * @param  array  $parameters
	 * @return mixed|\Illuminate\Foundation\Application
	 */
	function app($abstract = null, array $parameters = [])
	{
		if (is_null($abstract)) {
			return Container::getInstance();
		}
		
		return Container::getInstance()->make($abstract, $parameters);
	}
}

if (!function_exists('jd')) {
	function jd(...$vars)
	{
		$option = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
		$traces = debug_backtrace();
		
		$args = [
			"{$traces[0]['file']}:{$traces[0]['line']}",
		];
		foreach ($vars as $var) {
			if ($var instanceof \Illuminate\Database\Eloquent\Model ||
				$var instanceof \Illuminate\Database\Eloquent\Collection) {
				$args[] = $var->toJson($option);
			} else {
				$args[] = json_encode($var, $option);
			}
		}
		dd(...$args);
	}
}

if (!function_exists('ll')) {
	/**
	 * 不退出打印
	 * @param       $var
	 * @param array ...$moreVars
	 */
	function ll($var, ...$moreVars)
	{
		$traces = debug_backtrace();
		
		VarDumper::dump("{$traces[0]['file']}:{$traces[0]['line']}");
		VarDumper::dump($var);
		foreach ($moreVars as $v) {
			VarDumper::dump($v);
		}
	}
}