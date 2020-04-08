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

use W7\App\Model\Entity\Document\ChapterRecord;

class ChapterRecordService
{
	protected $record;

	public function __construct($record)
	{
		$this->record = $record;
	}

	public function recordToMarkdown($chapter_id)
	{
		//markdown数据-初始化顺序
		$markdown = [
			'api' => '',
			'apiHeader' => '',
			'apiParam' => '',
			'apiSuccess' => '',
			'apiExtend' => '',
		];

		foreach ($this->record as $key => $val) {
			if (is_array($val)) {
				if ($key == 'api') {
					$markdown['api'] = $this->buildApi($chapter_id, $val);
				} elseif ($key == 'apiHeader') {
					$markdown['apiHeader'] = $this->buildApiHeader($chapter_id, $val);
				} elseif ($key == 'apiParam') {
					$markdown['apiParam'] = $this->buildApiParam($chapter_id, $val);
				} elseif ($key == 'apiSuccess') {
					$markdown['apiSuccess'] = $this->buildApiSuccess($chapter_id, $val);
				}
			} else {
				if ($key == 'apiExtend') {
					$markdown['apiExtend'] = $val;
				}
			}
		}
		$markdownText = implode("\n", $markdown);
		return $markdownText;
	}

	public function buildApiSuccess($chapter_id, $data)
	{
		$text = "### 返回参数\n\n";
		$text = $text . $this->strLengthAdaptation('参数名称', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('类型', ChapterRecord::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterRecord::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:-', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterRecord::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterRecord::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		foreach ($data as $k => $val) {
			$text .= $this->buildParamChildren($val);
		}
		return $text;
	}

	public function getChildrenTop($level)
	{
		return str_repeat('&emsp;', $level);
	}

	public function buildParamChildren($data, $level = 0)
	{
		$childrenTop = $this->getChildrenTop($level);
		$key = $childrenTop . '';
		$type = '';
		$value = '';
		$description = '';
		$enabled = 0;
		if (isset($data['key'])) {
			$key = $childrenTop . $data['key'];
		}
		if (isset($data['type'])) {
			$type = $data['type'];
		}
		if (isset($data['enabled'])) {
			$enabled = $data['enabled'];
		}
		if (isset($data['value'])) {
			$value = $data['value'];
		}
		if (isset($data['description'])) {
			$description = $data['description'];
		}
		$enabledText = $this->getEnabledText($enabled);
		$text = $this->strLengthAdaptation($key, ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($type, ChapterRecord::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation($enabledText, ChapterRecord::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($value, ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		if (isset($data['children']) && (!empty($data['children'])) && is_array($data['children'])) {
			foreach ($data['children'] as $k => $val) {
				$text .= $this->buildParamChildren($val, $level + 1);
			}
		}
		return $text;
	}

	public function buildApiParam($chapter_id, $data)
	{
		$text = "### 请求参数\n\n";
		$text = $text . $this->strLengthAdaptation('参数名称', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('类型', ChapterRecord::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterRecord::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:-', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterRecord::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterRecord::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		foreach ($data as $k => $val) {
			$text .= $this->buildParamChildren($val);
		}
		return $text;
	}

	public function getEnabledText($enabled)
	{
		$enabledLabel = ChapterRecord::getEnabledLabel();
		return $enabledLabel[$enabled];
	}

	public function buildApiHeader($chapter_id, $data)
	{
		$text = "### 请求头\n\n";
		$text = $text . $this->strLengthAdaptation('参数名称', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterRecord::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:-', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterRecord::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		foreach ($data as $k => $val) {
			$key = '';
			$value = '';
			$description = '';
			$enabled = 0;
			if (isset($val['key'])) {
				$key = $val['key'];
			}
			if (isset($val['enabled'])) {
				$enabled = $val['enabled'];
			}
			if (isset($val['value'])) {
				$value = $val['value'];
			}
			if (isset($val['description'])) {
				$description = $val['description'];
			}
			$enabledText = $this->getEnabledText($enabled);
			$text .= $this->strLengthAdaptation($key, ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($enabledText, ChapterRecord::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($value, ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		}
		return $text;
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

	public function buildApi($chapter_id, $data)
	{
		$type = 'get';
		$value = '';
		$description = '';
		if (isset($data['type'])) {
			$type = $data['type'];
		}
		if (isset($data['value'])) {
			$value = $data['value'];
		}
		if (isset($data['description'])) {
			$description = $data['description'];
		}
		if ($description) {
			$text = '- **接口说明：** ' . $description . "\n- **接口地址：** " . $value . "\n- **请求方式：** ==" . $type . "==\n";
		} else {
			$text = '**接口地址：** ' . $value . "\n- **请求方式：** ==" . $type . "==\n";
		}
		return $text;
	}
}
