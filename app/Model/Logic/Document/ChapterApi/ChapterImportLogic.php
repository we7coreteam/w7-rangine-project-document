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

class ChapterImportLogic extends ChapterCommonLogic
{
	public function getApiparam($data, $reponseId, $type = 'key_word')
	{
		if ($type == 'key_word') {
			$array = $this->keyWordToData($data);
		} elseif ($type == 'json') {
			$array = json_decode($data, true);
		} else {
			$array = $data;
		}

		$record=[];
		if (is_array($array)){
			//生成Apiparam数据
			foreach ($array as $key =>$val){

			}
			return $record;
		}
		throw new ErrorHttpException('导入数据不符合要求');
	}

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
