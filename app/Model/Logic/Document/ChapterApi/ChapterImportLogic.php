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

namespace W7\App\Model\Logic\Document\ChapterApi;

//返回演示数据demo

use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document\ChapterApiParam;

class ChapterImportLogic extends ChapterCommonLogic
{
	private $errNullMsg = '导入数据不能为空';

	public function getApiparam($data, $location, $type = 'key_word')
	{
		if ($type == 'key_word') {
			if (!is_array($data)) {
				$array = $this->keyWordToData($data);
				if ($data && count($array) == 0) {
					//兼容文本长内容，必须要用:隔开
					throw new ErrorHttpException('键值对格式错误，请按照key:value格式填写');
				}
			} else {
				throw new ErrorHttpException('键值对格式错误，请按照key:value格式填写');
			}
		} elseif ($type == 'json') {
			if (!$data) {
				throw new ErrorHttpException($this->errNullMsg);
			}
			if ($this->isJson($data)) {
				$array = json_decode($data, true);
			} else {
				throw new ErrorHttpException('导入数据不是标准的JSON格式');
			}
		} else if (is_array($data)) {
			$array = $data;
		}
		if (is_array($array)) {
			if (count($array) == 0) {
				throw new ErrorHttpException($this->errNullMsg);
			}
			//生成Apiparam数据
			$record = $this->formartToMock($array, $location);
			return $record;
		}
		throw new ErrorHttpException('导入数据不符合要求');
	}

	/**
	 * 导入参数格式化成mock
	 */
	public function formartToMock(array $arr, $location, $mergeRecursive = [])
	{
		$data = [];
		foreach ($arr as $k => $v) {
			$children = [];
			$arrayData = [];
			$default = $v;
			$type = ChapterApiParam::TYPE_STRING;
			if (is_numeric($v)) { //数字
				$type = ChapterApiParam::TYPE_NUMBER;
			} elseif (is_bool($v)) { //布尔
				$type = ChapterApiParam::TYPE_BOOLEAN;
			} elseif (is_string($v)) { //字符串
				$type = ChapterApiParam::TYPE_STRING;
			} elseif (is_null($v)) {
				$type = ChapterApiParam::TYPE_NULL;
			} elseif (is_array($v)) { //数组或对象
				$arrayData = $this->buildArrayData($k, $v, $location);
			}

			if ($mergeRecursive && isset($mergeRecursive[$k])) {
				//去重
				if (is_array($mergeRecursive[$k])) {
					$uniqueMergeRecursive = array_unique($mergeRecursive[$k]);
					if (count($uniqueMergeRecursive) > 1) {
						if ($type !== ChapterApiParam::TYPE_STRING) {
							//如果
							$type = $this->checkType($type, $uniqueMergeRecursive);
						}
						$default = json_encode($uniqueMergeRecursive, true);
					} else {
						$default = $uniqueMergeRecursive[0];
					}
				} else {
					$default = $mergeRecursive[$k];
				}
			}

			if ($arrayData) {
				$data[] = $arrayData;
			} else {
				$data[] = [
					'type' => $type,
					'name' => $k,
					'description' => '',
					'enabled' => ChapterApiParam::ENABLED_YES,
					'location' => $location,
					'default_value' => $default,
					'rule' => $rule ?? '',
					'children' => $children ?? []
				];
			}
		}
		return $data;
	}

	public function checkType($oldtype, $uniqueMergeRecursive)
	{
		foreach ($uniqueMergeRecursive as $k => $v) {
			if (is_numeric($v)) { //数字
				$type = ChapterApiParam::TYPE_NUMBER;
			} elseif (is_bool($v)) { //布尔
				$type = ChapterApiParam::TYPE_BOOLEAN;
			} elseif (is_string($v)) { //字符串
				$type = ChapterApiParam::TYPE_STRING;
			} elseif (is_null($v)) {
				$type = ChapterApiParam::TYPE_NULL;
			} else {
				$type = $oldtype;
			}
			if ($type != $oldtype) {
				//如果出现两种以上类型->直接返回字符串
				return ChapterApiParam::TYPE_STRING;
			}
		}
		return $oldtype;
	}

	public function buildArrayData($key, $val, $location)
	{
		$default = '';
		$rule = 0;
		$type = ChapterApiParam::TYPE_ARRAY;
		$i = 0;
		$sunArray = [];
		$mergeRecursive = [];
		$hasArray = 0;
		foreach ($val as $k => $v) {
			if ($k != $i) {
				$type = ChapterApiParam::TYPE_OBJECT;
				break;
			}
			$i++;
			//如果不是对象，是多维数组
			if (is_array($v) && $type == ChapterApiParam::TYPE_ARRAY) {
				$hasArray = 1;
				//如果子数组是个数组
				$rule = $rule + 1;
				if ($rule == 1) {
					$sunArray = $v;
					$mergeRecursive = $v;
				} else {
					//值和键值分开
					$sunArray = array_merge($sunArray, $v);
					//合并的时候，值合并到一起(按数组)
					$mergeRecursive = array_merge_recursive($mergeRecursive, $v);
				}
			}
			if (!is_array($v) && $hasArray == 1) {
				$type = ChapterApiParam::TYPE_OBJECT;
				//如果都是数组（多维数组）当前有一个不是数组->是对象
				break;
			}
		}
		if ($type == ChapterApiParam::TYPE_OBJECT) {
			//如果当前数组是对象
			return [
				'type' => ChapterApiParam::TYPE_OBJECT,
				'name' => $key,
				'description' => '',
				'enabled' => ChapterApiParam::ENABLED_YES,
				'default_value' => $default,
				'rule' => '',
				'children' => $this->formartToMock($val, $location)
			];
		} else if ($rule > 0) {
			//如果是多维数组
			return [
				'type' => ChapterApiParam::TYPE_ARRAY,
				'name' => $key,
				'description' => '',
				'enabled' => ChapterApiParam::ENABLED_YES,
				'default_value' => $rule,
				'rule' => '',
				'children' => $this->formartToMock($sunArray, $location, $mergeRecursive)
			];
		} else {
			//单数组或者对象
			if ($this->is_assoc($val)) {
				//如果是对象
				return [
					'type' => ChapterApiParam::TYPE_OBJECT,
					'name' => $key,
					'description' => '',
					'enabled' => ChapterApiParam::ENABLED_YES,
					'default_value' => $default,
					'rule' => '',
					'children' => $this->formartToMock($val, $location)
				];
			} else {
				//如果是纯数组，默认值返回整个数组
				if (array_unique($val)) {
					$default = $this->dataToJson(array_unique($val));
				}
				return [
					'type' => ChapterApiParam::TYPE_ARRAY,
					'name' => $key,
					'description' => '',
					'enabled' => ChapterApiParam::ENABLED_YES,
					'default_value' => $default,
					'rule' => '',
					'children' => []
				];
			}
		}
	}


//	public function buildArrayData($v){
//		if (count($v) == count($v, 1)) { //数组
//			$type = ChapterApiParam::TYPE_ARRAY;
//			if ($step) { //纯数字数组
//				$default = $v === array_filter($v, 'is_int') ? current($v) : $v;
//				if (is_array($default)) {
//					//如果是数组转JSON
//					$default = $this->dataToJson($default);
//				}
//			} elseif (!($v === array_filter($v, 'is_int'))) { //单个对象
//				$children = $this->formartToMock($v, $location);
//				$default = '';
//			}
//		} elseif($this->is_assoc($v)) {
//			$children = $this->formartToMock($v, $location, true);
//		}else{ //对象集合
//			$type = ChapterApiParam::TYPE_OBJECT;
//			$merge = [];
//			foreach ($v as $v1) {
//				$merge = array_merge_recursive($merge, $v1);
//			}
//			foreach ($merge as &$v1) {
//				$v1 = array_pad(is_array($v1) ? $v1 : [$v1], count($v), null);
//			}
//			if (!$this->is_assoc($merge)) {
//				$rule = count($v);
//			} else {
//				$children = $this->formartToMock($merge, $location, true);
//			}
//			$default = '';
//		}
//	}

	public function dataToJson($arr)
	{
		foreach ($arr as $key => $val) {
			if (is_numeric($val)) {
				$arr[$key] = (int)$val;
			}
		}
		return json_encode($arr, JSON_UNESCAPED_UNICODE);
	}

//	public function buildApiparamData($data)
//	{
//		$record = [];
//		foreach ($data as $key => $val) {
//			if (is_array($val)) {
//				//多维数组还是单维度数组
//				if (count($val) == count($val, 1)) {
//					if ($this->is_assoc($val)) {
//						//对象
//						$record[] = [
//							'type' => ChapterApiParam::TYPE_OBJECT,
//							'name' => $key,
//							'description' => '',
//							'enabled' => ChapterApiParam::ENABLED_YES,
//							'default_value' => '',
//							'rule' => '',
//							'children' => $this->buildApiparamData($val)
//						];
//					} else {
//						//纯数组
//						$record[] = [
//							'type' => ChapterApiParam::TYPE_ARRAY,
//							'name' => $key,
//							'description' => '',
//							'enabled' => ChapterApiParam::ENABLED_YES,
//							'default_value' => json_encode($val),
//							'rule' => '',
//							'children' => []
//						];
//					}
//				} else {
//					//多维数组
//					//多维数组先合并子类
//					$sun = [];
//					foreach ($val as $k => $v) {
//
//						if ($this->is_assoc($val)) {
//							//对象
//							$sun[$k][] = $v;
//						} else {
//							//数组
//							$sun[$k][] = $v;
//						}
//					}
//					$record[] = [
//						'type' => ChapterApiParam::TYPE_ARRAY,
//						'name' => $key,
//						'description' => '',
//						'enabled' => ChapterApiParam::ENABLED_YES,
//						'default_value' => '',
//						'rule' => '+' . count($val),
//						'children' => $this->buildApiparamData($val)
//					];
//				}
//			} else {
//				//键值
//				$type = ChapterApiParam::TYPE_STRING;
//				if (is_numeric($val)) {
//					$type = ChapterApiParam::TYPE_NUMBER;
//				} elseif ($val == 'true' || $val == 'false') {
//					$type = ChapterApiParam::TYPE_BOOLEAN;
//				}
//				$record[] = [
//					'type' => $type,
//					'name' => $key,
//					'description' => '',
//					'enabled' => ChapterApiParam::ENABLED_YES,
//					'default_value' => $val,
//					'rule' => '',
//					'children' => []
//				];
//			}
//		}
//		return $record;
//	}

	public function keyWordToData($keyWord)
	{
		$data = [];
		$str = '';
		$data1 = explode("\n", $keyWord);
		if (count($data1)) {
			$data1 = $this->compatible($data1);
			foreach ($data1 as $key => $val) {
				$item = '';
				$itemData = explode(':', $val);
				if (substr($itemData[0], 0, 2) == '//') {
					continue;
				}
				if (count($itemData) > 1) {
					$start = strlen($itemData[0] . ':');
					$last = substr($val, $start);
					//首字符去空
//					if (strlen($last) > 1) {
//						if (substr($last, 0, 1) == ' ') {
//							$last = substr($last, 1);
//						}
//					}
					$item = $itemData[0] . '=' . $last;
				} else {
					$item = $itemData[0] . '=';
				}
				if ($item) {
					parse_str($item, $dataTemp);
					//单条转化
					$data = $this->buildData($data, $dataTemp);
				}
			}
		}
		return $data;
	}

	//兼容文本
	public function compatible($data)
	{
		$i = 0;
		$last = '';
		$newData = [];
		foreach ($data as $key => $val) {
			if (strpos($val, ':') !== false) {
				$newData[$i] = $val;
				$last = $val;
				$i++;
			} else {
				//不包含:
				if ($i > 0) {
					$newData[$i - 1] = $last . "\n" . $val;
					$last = $newData[$i - 1];
				}
			}
		}
		return $newData;
	}

	//组数组
	public function buildData($data, $dataTemp)
	{
		foreach ($dataTemp as $k => $v) {
			if (is_array($v)) {
				//如果是数组
				if (isset($data[$k])) {
					$data[$k] = $this->buildData($data[$k], $v);
				} else {
					$data[$k] = $this->buildData([], $v);
				}
			} else {
				$data[$k] = $v;
			}
		}
		return $data;
	}
}
