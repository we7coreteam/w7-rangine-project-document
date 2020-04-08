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
use W7\App\Model\Entity\Document\ChapterApiParam;

class ChapterRecordService
{
	protected $chapterId;
	protected $record;

	public function __construct($chapterId, $record)
	{
		$this->chapterId = $chapterId;
		$this->record = $record;
	}

	public function recordToMarkdown()
	{
		//markdown数据-初始化顺序
		$markdown = [
			'api' => '',
			'apiBody' => '',
			'apiExtend' => '',
		];
		idb()->beginTransaction();
		try {
			foreach ($this->record as $key => $val) {
				if (is_array($val)) {
					if ($key == 'api') {
						$markdown['api'] = $this->buildApi($val);
					} elseif ($key == 'apiBody') {
						$markdown['apiBody'] = $this->buildApiBody($val);
					}
				} else {
					if ($key == 'apiExtend') {
						$markdown['apiExtend'] = $val;
					}
				}
			}
			idb()->commit();
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage(), $e->getCode());
		}
		$markdownText = implode("\n", $markdown);
		return $markdownText;
	}

	public function buildApiBody($data)
	{
		ksort($data);
		$text = '';
		$outData = [
			'Request.Header',
			'Request.Body',
			'Reponse.Header',
			'Reponse.Body',
		];
		return $text;
	}

	public function headTableTop()
	{
		$text = $this->strLengthAdaptation('参数名称', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation('生成规则', ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:-', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
		return $text;
	}

	public function buildApiHeader($data)
	{
		$chapterId = $this->chapterId;
		$text = "### 请求头\n\n";
		$text = $text . $this->headTableTop();
		$ids = [];
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
			$location = ChapterApiParam::LOCATION_REQUEST_HEADER;
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
				if ($chapterApiParam && $chapterApiParam->chapter_id == $chapterId) {
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
		//循环结束以后，删除该章节本次未提交的ID
		if ($ids) {
			ChapterApiParam::query()->where('chapter_id', $chapterId)->where('location', $location)->whereNotIn('id', $ids)->delete();
		}
		return $text;
	}

	public function buildApiSuccess($data)
	{
		$chapterId = $this->chapterId;
		$text = "### 返回参数\n\n";
		$text = $text . $this->bodyTableTop();
		foreach ($data as $k => $val) {
			$text .= $this->buildParamChildren($val);
		}
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

	public function buildParamChildren($data, $level = 0)
	{
		$childrenTop = $this->getChildrenTop($level);
		$name = $childrenTop . '';
		$type = '';
		$default_value = '';
		$description = '';
		$rule = '';
		$enabled = 1;
		if (isset($data['name'])) {
			$name = $childrenTop . $data['name'];
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
		$text = $this->strLengthAdaptation($name, ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($typeText, ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation($enabledText, ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($default_value, ChapterApiParam::TABLE_VALUE_LENGTH) . '|' . $this->strLengthAdaptation($rule, ChapterApiParam::TABLE_RULE_LENGTH) . "\n";
		if (isset($data['children']) && (!empty($data['children'])) && is_array($data['children'])) {
			foreach ($data['children'] as $k => $val) {
				$text .= $this->buildParamChildren($val, $level + 1);
			}
		}
		return $text;
	}

	public function buildApiParam($data)
	{
		$chapterId = $this->chapterId;
		$text = "### 请求参数\n\n";
		$text = $text . $this->bodyTableTop();
		foreach ($data as $k => $val) {
			$text .= $this->buildParamChildren($val);
		}
		return $text;
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
			'description' => $description
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
