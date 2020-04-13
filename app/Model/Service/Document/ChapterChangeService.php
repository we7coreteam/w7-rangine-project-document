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

namespace W7\App\Model\Service\Document;

use W7\App\Exception\ErrorHttpException;

/**
 * 数据转换0自适应，1json2数组（非键值对数组，纯数组）3键值对文本
 */
class ChapterChangeService extends ChapterCommonService
{
	public function textToData($text, $type = 0)
	{
		if (!$text) {
			return [];
		} elseif (is_array($text)) {
			//如果是数组
			$type = 2;
			if ($type != 2 && $type === 0) {
				throw new ErrorHttpException('当前数据不是数组格式');
			}
		} elseif ($this->isJson($text)) {
			//如果是json或不存在
			$type = 1;
			if ($type != 1 && $type === 0) {
				throw new ErrorHttpException('当前数据不是json格式');
			}
		}

		if ($type == 1) {
			//如果是json-转数组
			$data = json_decode($text, true);
		} elseif ($type == 2) {
			//如果是数组格式
			$data = $text;
		} else {
			//键值对文本
			$data = $this->getKeyValueToArray($text);
		}
		//数组转列表数据
		if (is_array($data)) {
			return $this->arrayToData($data);
		}
		return false;
	}

	public function getKeyValueToArray($str)
	{
		//键值对数组
		$info1 = explode("\n", $str);
		$reply = [];
		foreach ($info1 as $key => $val) {
			$info2 = explode(':', $val);
			$name = urldecode($info2[0]);
			$value = urldecode(str_replace($info2[0] . ':', '', $val));
			$reply[$key] = urlencode($name) . '=' . urlencode($value);
		}
		//http参数
		$newStr = implode('&', $reply);
		parse_str($newStr, $result);
		return $result;
	}

	//数组转列表数据
	public function arrayToData($inputData, $descriptionData)
	{
		if (!$inputData) {
			return [];
		}
		if ((!$this->is_assoc($inputData)) && (count($inputData) != count($inputData, 1))) {
			//不是对象，并且不是一维数组
			$n = 0;
			$data = $this->getArrayToDataChildrenMany($inputData, $n, $descriptionData);
		} else {
			$data = $this->getArrayToDataChildren($inputData, 0, $descriptionData);
		}
		return $data;
	}

	//多维数组转换[{"a":"1","b":"2"},{"c":"3"}]
	public function getArrayToDataChildrenMany($inputData, &$n, $descriptionData = [])
	{
		$dataRow = [];
		foreach ($inputData as $key => $val) {
			//每行数据
			foreach ($val as $k => $v) {
				if (is_array($v)) {
					$dataRow[$k] = $v;
				} else {
					if (isset($dataRow[$k]) && $dataRow[$k]) {
						//第二次-获取默认值-加入值列表-去重
						$rowVal = json_decode($dataRow[$k], true);
						$rowVal[count($rowVal)] = $v;
						$dataRow[$k] = json_encode(array_unique($rowVal));
					} else {
						//第一次
						$dataRow[$k] = json_encode([$v]);
					}
				}
			}
			$n++;
		}
		$data = $this->getArrayToDataChildren($dataRow, 1, $descriptionData);
		return $data;
	}

	//普通转换many=0默认=1多维数组=2单数组无键值
	public function getArrayToDataChildren($inputData, $many = 0, $descriptionData = [])
	{
		$data = [];
		foreach ($inputData as $key => $val) {
			$description = '';
			$descriptionRow = [];
			if (isset($descriptionData[$key])) {
				if (is_array($descriptionData[$key])) {
					$descriptionRow = $descriptionData[$key];
				} else {
					$description = $descriptionData[$key];
				}
			}
			if (is_array($val)) {
				if ($this->is_assoc($val)) {
					//判断键值对-非数组-对象{"0":"b","b":"4"}
					$data[] = [
						'name' => $key,
						'type' => 4,
						'description' => $description,
						'enabled' => 1,
						'default_value' => '',
						'rule' => '',
						'children' => $this->getArrayToDataChildren($val, 0, $descriptionRow)
					];
				} else {
					//判断键值对-数组["1","a"]
					if (count($val) == count($val, 1)) {
						//一维
						$oneArray = [
							'name' => $key,
							'type' => 5,
							'description' => $description,
							'enabled' => 1,
							'default_value' => $val,
							'rule' => ''
						];
						if (count($val) > 1) {
							$oneArray['children'] = $this->getArrayToDataChildren($val, 2, $descriptionRow);
						}
						$data[] = $oneArray;
					} else {
						//多维[{"a":"1","b":"2"},{"c":"3"}]
						$n = 0;
						$manyArray = [
							'name' => $key,
							'type' => 5,
							'description' => $description,
							'enabled' => 1,
							'default_value' => '',
							'rule' => '',
							'children' => $this->getArrayToDataChildrenMany($val, $n, $descriptionRow)
						];
						if ($n > 0) {
							$manyArray['rule'] = $n;
						}
						$data[] = $manyArray;
					}
				}
			} else {
				$type = 1;
				$rule = '';
				if ($val == null) {
					$type = 8;
				} elseif (is_numeric($val)) {
					$type = 2;
				} elseif ($val === true || $val === false) {
					$type = 3;
				}
				if ($many == 1) {
					//如果是多维数组-强制单个字段类型为数组
					$type = 5;
					if ($this->isJson($val)) {
						if (count(json_decode($val)) > 1) {
							$rule = '+1';
						}
					}
				}
				if ($many == 2) {
					$key = '';
				}
				$data[] = [
					'name' => $key,
					'type' => $type,
					'description' => $description,
					'enabled' => 1,
					'default_value' => $val,
					'rule' => $rule
				];
			}
		}
		return $data;
	}
}
