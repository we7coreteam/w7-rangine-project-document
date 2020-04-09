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

use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Entity\Document\ChapterApiExtend;
use W7\App\Model\Entity\Document\ChapterApiParam;

class ChapterRecordService
{
	protected $chapterId;
	protected $ids = [];
	protected $bodyParamLocation = 3;
	protected $bodyReponseLocation = 10;

	public function __construct($chapterId)
	{
		$this->chapterId = $chapterId;
	}

	public function textToData($text, $type = 0)
	{
		if ($this->isJson($text) || (!$text)) {
			//如果是json或不存在
			$type = 1;
		} else {
			//如果不是JSON指定了json
			if ($type == 1) {
				throw new ErrorHttpException('当前数据不是json格式');
			}
		}
		if ($type == 1) {
			return $this->jsonToData($text);
		}
		return $this->keyValueToData($text);
	}

	public function isJson($data = '', $assoc = false)
	{
		$data = json_decode($data, $assoc);
		if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
			return $data;
		}
		return false;
	}

	//键值对导入转列表数组
	public function keyValueToData($text)
	{
		$data = [];
		$strData = [];
		$inputData = explode("\n", $text);
		foreach ($inputData as $key => $val) {
			$rowdata = explode(':', $val);
			$strData[$rowdata[0]] = $rowdata[1];
		}
		$i = 0;
		foreach ($strData as $key => $val) {
			preg_match_all("/(?:\[)(.*)(?:\])/i", $key, $result);
			$data[$i++] = $result;
			//去]然后按[分割
		}
		return $strData;
	}

	public function is_assoc($arr)
	{
		//array(1, 2, 3, 4, 5, 6, 7);// 输出false
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	//json导入转列表数组
	public function jsonToData($json)
	{
		if (!$json) {
			return [];
		}
		$inputData = json_decode($json, true);
		if ((!$this->is_assoc($inputData)) && (count($inputData) != count($inputData, 1))) {
			//不是对象，并且不是一维数组
			$data = $this->getjsonToDataChildrenMany($inputData);
		} else {
			$data = $this->getjsonToDataChildren($inputData);
		}
		return $data;
	}

	//多维数组转换[{"a":"1","b":"2"},{"c":"3"}]
	public function getjsonToDataChildrenMany($inputData)
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
		}
		$data = $this->getjsonToDataChildren($dataRow, 1);
		return $data;
	}

	//普通转换
	public function getjsonToDataChildren($inputData, $many = 0)
	{
		$data = [];
		foreach ($inputData as $key => $val) {
			if (is_array($val)) {
				if ($this->is_assoc($val)) {
					//判断键值对-非数组-对象{"0":"b","b":"4"}
					$data[] = [
						'name' => $key,
						'type' => 4,
						'description' => '',
						'enabled' => 1,
						'default_value' => '',
						'rule' => '',
						'children' => $this->getjsonToDataChildren($val)
					];
				} else {
					//判断键值对-数组["1","a"]
					if (count($val) == count($val, 1)) {
						//一维
						$data[] = [
							'name' => $key,
							'type' => 5,
							'description' => '',
							'enabled' => 1,
							'default_value' => $val,
							'rule' => ''
						];
					} else {
						//多维[{"a":"1","b":"2"},{"c":"3"}]
						$data[] = [
							'name' => $key,
							'type' => 5,
							'description' => '',
							'enabled' => 1,
							'default_value' => '',
							'rule' => '',
							'children' => $this->getjsonToDataChildrenMany($val)
						];
					}
				}
			} else {
				$type = 1;
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
				}
				$data[] = [
					'name' => $key,
					'type' => $type,
					'description' => '',
					'enabled' => 1,
					'default_value' => $val,
					'rule' => ''
				];
			}
		}
		return $data;
	}

	public function copyRecord($newChapterId)
	{
		$chapterId = $this->chapterId;
		$chapterApi = ChapterApi::query()->where('chapter_id', $chapterId)->first();
		if ($chapterApi) {
			$chapterApi->chapter_id = $newChapterId;
			ChapterApi::query()->create($chapterApi->toArray());
			$chapterApiParam = ChapterApiParam::query()->where('chapter_id', $chapterId)->where('parent_id', 0)->get();
			if ($chapterApiParam) {
				foreach ($chapterApiParam as $key => $val) {
					$val->chapter_id = $newChapterId;
					$newChapterApiParam = ChapterApiParam::query()->create($val->toArray());
					$val->children = $this->copyBodyChildren($chapterId, $val->id, $newChapterId, $newChapterApiParam->id);
				}
			}
			$chapterApiExtend = ChapterApiExtend::query()->where('chapter_id', $chapterId)->first();
			if ($chapterApiExtend) {
				$chapterApiExtend->chapter_id = $newChapterId;
				ChapterApiExtend::query()->create($chapterApiExtend->toArray());
			}
		}
		return true;
	}

	public function copyBodyChildren($chapterId, $parentId, $newChapterId, $newParentId)
	{
		$chapterApiParam = ChapterApiParam::query()->where('chapter_id', $chapterId)->where('parent_id', $parentId)->get();
		if ($chapterApiParam) {
			foreach ($chapterApiParam as $key => $val) {
				$val->chapter_id = $newChapterId;
				$val->parent_id = $newParentId;
				$newChapterApiParam = ChapterApiParam::query()->create($val->toArray());
				$val->children = $this->copyBodyChildren($chapterId, $val->id, $newChapterId, $newChapterApiParam->id);
			}
			return $chapterApiParam;
		}
		return [];
	}

	public function showRecord()
	{
		$chapterId = $this->chapterId;
		$record = [
			'api' => [],
			'body' => [],
			'extend' => ''
		];
		$body = [];
		$chapterApi = ChapterApi::query()->where('chapter_id', $chapterId)->first();
		if ($chapterApi) {
			$record['api'] = $chapterApi;
			$chapterApiParam = ChapterApiParam::query()->where('chapter_id', $chapterId)->where('parent_id', 0)->get();
			if ($chapterApiParam) {
				foreach ($chapterApiParam as $key => $val) {
					$val->children = $this->getBodyChildren($chapterId, $val->id);
					$body[$val->location][] = $val;
				}
				ksort($body);
				$record['body'] = $body;
			}
			$chapterApiExtend = ChapterApiExtend::query()->where('chapter_id', $chapterId)->first();
			if ($chapterApiExtend) {
				$record['extend'] = $chapterApiExtend->extend;
			}
		}
		return $record;
	}

	public function getBodyChildren($chapterId, $parentId)
	{
		$chapterApiParam = ChapterApiParam::query()->where('chapter_id', $chapterId)->where('parent_id', $parentId)->get();
		if ($chapterApiParam) {
			foreach ($chapterApiParam as $key => $val) {
				$val->children = $this->getBodyChildren($chapterId, $val->id);
				$body[$val->location][] = $val;
			}
			return $chapterApiParam;
		}
		return [];
	}

	public function recordToMarkdown($record)
	{
		//markdown数据-初始化顺序
		$markdown = [
			'api' => '',
			'body' => '',
			'extend' => '',
		];
		idb()->beginTransaction();
		try {
			foreach ($record as $key => $val) {
				if (is_array($val)) {
					if ($key == 'api') {
						$markdown['api'] = $this->buildApi($val);
					} elseif ($key == 'body') {
						$markdown['body'] = $this->buildBody($val);
					}
				} else {
					if ($key == 'extend') {
						$markdown['extend'] = $this->buildExtend($val);
					}
				}
			}
			//循环结束以后，删除该父级本次未提交的ID
			$ids = $this->ids;
			$chapterId = $this->chapterId;
			if ($ids) {
				ChapterApiParam::query()->where('chapter_id', $chapterId)->whereNotIn('id', $ids)->delete();
			}
			idb()->commit();
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage(), $e->getCode());
		}
		$markdownText = implode("\n", $markdown);
		return $markdownText;
	}

	public function buildExtend($data)
	{
		$chapterId = $this->chapterId;
		$saveData = [
			'chapter_id' => $chapterId,
			'extend' => $data,
		];
		$chapterApiExtend = ChapterApiExtend::query()->where('chapter_id', $chapterId)->first();
		if ($chapterApiExtend) {
			$chapterApiExtend->update($saveData);
		} else {
			ChapterApiExtend::query()->create($saveData);
		}
		return $data;
	}

	public function buildBody($data)
	{
		ksort($data);
		$text = '';
		foreach ($data as $k => $v) {
			if (in_array($k, [ChapterApiParam::LOCATION_REQUEST_HEADER, ChapterApiParam::LOCATION_REPONSE_HEADER])) {
				$text .= $this->buildApiHeader($k, $v);
			} elseif (in_array($k, [ChapterApiParam::LOCATION_REQUEST_QUERY])) {
				$text .= $this->buildApiBody($k, $v);
			} elseif ($k == $this->bodyParamLocation) {
				$text .= $this->buildApiBody($k, $v);
			} elseif ($k == $this->bodyReponseLocation) {
				$text .= $this->buildApiBody($k, $v);
			}
		}
		return $text;
	}

	public function buildBodyChildren($location, $data, $level = 0, $parentId = 0)
	{
		$childrenTop = $this->getChildrenTop($level);
		$name = '';
		$type = 1;
		$default_value = '';
		$description = '';
		$rule = '';
		$enabled = 1;
		if (isset($data['name'])) {
			$name = $data['name'];
		}
		if (isset($data['type'])) {
			$type = $data['type'];
		}
		if (isset($data['enabled'])) {
			$enabled = $data['enabled'];
		}
		if (isset($data['default_value'])) {
			$default_value = $data['default_value'];
		}
		if (isset($data['description'])) {
			$description = $data['description'];
		}

		if (isset($data['rule'])) {
			$rule = $data['rule'];
		}

		$enabledText = $this->getEnabledText($enabled);
		$typeText = $this->getTypeText($type);
		$text = $this->strLengthAdaptation($childrenTop . $name, ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($typeText, ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation($enabledText, ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($default_value, ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation($rule, ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
		//存储
		$ids = $this->ids;
		$chapterId = $this->chapterId;
		$saveData = [
			'chapter_id' => $chapterId,
			'parent_id' => $parentId,
			'location' => $location,
			'type' => $type,
			'name' => $name,
			'description' => $description,
			'enabled' => $enabled,
			'default_value' => $default_value,
			'rule' => $rule
		];
		$id = $parentId;
		if (isset($data['id']) && $data['id']) {
			$ids[count($ids)] = $data['id'];
			$id = $data['id'];
			$chapterApiParam = ChapterApiParam::query()->find($data['id']);
			if ($chapterApiParam && $chapterApiParam->chapter_id == $chapterId && $chapterApiParam->location == $location) {
				$chapterApiParam->update($saveData);
			} else {
				throw new ErrorHttpException('当前保存的数据项已不存在！' . $data['id'] . '-' . $chapterId);
			}
		} else {
			$chapterApiParam = ChapterApiParam::query()->create($saveData);
			if ($chapterApiParam) {
				$ids[count($ids)] = $chapterApiParam->id;
				$id = $chapterApiParam->id;
			}
		}
		$this->ids = $ids;
		if (isset($data['children']) && (!empty($data['children'])) && is_array($data['children'])) {
			foreach ($data['children'] as $k => $val) {
				$text .= $this->buildBodyChildren($location, $val, $level + 1, $id);
			}
		}
		return $text;
	}

	public function buildApiBody($location, $data)
	{
		$title = $this->getLocatinonText($location);
		$text = '### ' . $title . "\n\n";
		$text = $text . $this->bodyTableTop();
		foreach ($data as $k => $val) {
			$text .= $this->buildBodyChildren($location, $val);
		}
		return $text;
	}

	public function getLocatinonText($location)
	{
		$typeLabel = ChapterApiParam::getLocationLabel();
		if (isset($typeLabel[$location])) {
			return $typeLabel[$location];
		}
		throw new ErrorHttpException('响应类型错误');
	}

	public function headTableTop()
	{
		$text = $this->strLengthAdaptation('参数名称', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation('生成规则', ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:-', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
		return $text;
	}

	public function buildApiHeader($location, $data)
	{
		$chapterId = $this->chapterId;
		$title = $this->getLocatinonText($location);
		$text = '### ' . $title . "\n\n";
		$text = $text . $this->headTableTop();
		$ids = $this->ids;
		foreach ($data as $k => $val) {
			$name = '';
			$default_value = '';
			$description = '';
			$enabled = 1;
			$rule = '';
			if (isset($val['name'])) {
				$name = $val['name'];
			}
			if (isset($val['enabled'])) {
				$enabled = $val['enabled'];
			}
			if (isset($val['default_value'])) {
				$default_value = $val['default_value'];
			}
			if (isset($val['description'])) {
				$description = $val['description'];
			}
			if (isset($val['rule'])) {
				$rule = $val['rule'];
			}
			$enabledText = $this->getEnabledText($enabled);
			$text .= $this->strLengthAdaptation($name, ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($enabledText, ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($default_value, ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation($rule, ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
			//存储
			$saveData = [
				'chapter_id' => $chapterId,
				'parent_id' => 0,
				'location' => $location,
				'type' => ChapterApiParam::TYPE_STRING,
				'name' => $name,
				'description' => $description,
				'enabled' => $enabled,
				'default_value' => $default_value,
				'rule' => $rule
			];
			if (isset($val['id']) && $val['id']) {
				$ids[count($ids)] = $val['id'];
				$chapterApiParam = ChapterApiParam::query()->find($val['id']);
				if ($chapterApiParam && $chapterApiParam->chapter_id == $chapterId && $chapterApiParam->location == $location) {
					$chapterApiParam->update($saveData);
				} else {
					throw new ErrorHttpException('当前保存的数据项已不存在！' . $val['id'] . '-' . $chapterId);
				}
			} else {
				$chapterApiParam = ChapterApiParam::query()->create($saveData);
				if ($chapterApiParam) {
					$ids[count($ids)] = $chapterApiParam->id;
				}
			}
		}
		$this->ids = $ids;
		return $text;
	}

	public function bodyTableTop()
	{
		$text = $this->strLengthAdaptation('参数名称', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('类型', ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation('生成规则', ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:-', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
		return $text;
	}

	public function getChildrenTop($level)
	{
		return str_repeat('&emsp;', $level);
	}

	public function getTypeText($type)
	{
		$typeLabel = ChapterApiParam::getTypeLabel();
		if (isset($typeLabel[$type])) {
			return $typeLabel[$type];
		}
		throw new ErrorHttpException('参数类型错误');
	}

	public function getEnabledText($enabled)
	{
		$enabledLabel = ChapterApiParam::getEnabledLabel();
		if (isset($enabledLabel[$enabled])) {
			return $enabledLabel[$enabled];
		}
		throw new ErrorHttpException('必填类型错误');
	}

	public function strLengthAdaptation($str, $defaultLength = 20)
	{
		if (!$str) {
			$str = '';
		}
		$lengthAll = strlen($str);
		$lengthCn = mb_strlen($str);
		if ($lengthAll > $lengthCn) {
			$length = $lengthAll - ($lengthAll - $lengthCn) / 2;
		} else {
			$length = $lengthAll;
		}

		if ($length < $defaultLength) {
			$str = $str . str_repeat(' ', ($defaultLength - $length));
		}
		return $str;
	}

	public function buildApi($data)
	{
		$chapterId = $this->chapterId;
		$method = 0;
		$url = '';
		$description = '';
		$statusCode = 0;
		$bodyParamLocation = 3;
		if (isset($data['method'])) {
			$method = $data['method'];
		}
		$methodLabel = ChapterApi::getMethodLabel();
		if (!isset($methodLabel[$method])) {
			throw new ErrorHttpException('请求方式错误');
		}
		if (isset($data['url'])) {
			$url = $data['url'];
		}
		if (isset($data['description'])) {
			$description = $data['description'];
		}
		if (isset($data['body_param_location'])) {
			$bodyParamLocation = $data['body_param_location'];
			$this->bodyParamLocation = $bodyParamLocation;
		}
		if (isset($data['status_code'])) {
			$statusCode = $data['status_code'];
			$statusCodeLists = ChapterApi::getStatusCode();
			if (!in_array($statusCode, $statusCodeLists)) {
				throw new ErrorHttpException('状态码错误');
			}
		}
		if ($description) {
			$text = '- **接口说明：** ' . $description . "\n";
		}
		$text .= '- **接口地址：** ' . $url . "\n- **请求方式：** ==" . $methodLabel[$method] . "==\n";
		if ($statusCode) {
			$text .= '- **状态码：** ==' . $statusCode . "==\n";
		}
		//存储
		$saveData = [
			'chapter_id' => $chapterId,
			'url' => $url,
			'method' => $method,
			'description' => $description,
			'body_param_location' => $bodyParamLocation
		];
		$chapterApi = ChapterApi::query()->where('chapter_id', $chapterId)->first();
		if ($chapterApi) {
			$chapterApi->update($saveData);
		} else {
			ChapterApi::query()->create($saveData);
		}
		return $text;
	}
}
