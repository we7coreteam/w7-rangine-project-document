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

	public function recordToMarkdown()
	{
		//标准数据
		$data = [
			'api' => [
				'type' => 'get',
				'key' => '',
				'description ' => ''
			],
			'apiHeader' => [],
			'apiParam' => []
		];
		//markdown数据-初始化顺序
		$markdown = [
			'api' => '',
			'apiHeader' => '',
			'apiParam' => '',
			'apiSuccess' => ''
		];
		foreach ($this->record as $key => $val) {
			if (is_array($val)) {
				if ($key == 'api') {
					$markdown['api'] = $this->buildApi($val);
				} elseif ($key == 'apiHeader') {
					$markdown['apiHeader'] = $this->buildApiHeader($val);
				} elseif ($key == 'apiParam') {
					$markdown['apiParam'] = $this->buildApiParam($val);
				}
			}
		}

		$markdownText = implode("\n", $markdown);
		return $markdownText;
	}

	public function buildApiParam($data)
	{
		$text = "### 请求参数\n\n";
		$text = $text . $this->strLengthAdaptation('参数名称', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('类型', ChapterRecord::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterRecord::TABLE_MUST_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:---', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':---', ChapterRecord::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation(':---', ChapterRecord::TABLE_MUST_LENGTH) . '|' . $this->strLengthAdaptation(':---', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':---', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		foreach ($data as $k => $val) {
			$key = '';
			$type = '';
			$value = '';
			$description = '';
			$must = 0;
			if (isset($val['key'])) {
				$key = $val['key'];
			}
			if (isset($val['type'])) {
				$type = $val['type'];
			}
			if (isset($val['must'])) {
				$must = $val['must'];
			}
			if (isset($val['value'])) {
				$value = $val['value'];
			}
			if (isset($val['description'])) {
				$description = $val['description'];
			}
			$mustText = $this->getMustText($must);
			$text .= $this->strLengthAdaptation($key, ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($type, ChapterRecord::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation($mustText, ChapterRecord::TABLE_MUST_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($value, ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		}
		return $text;
	}

	public function getMustText($must)
	{
		$mustLabel = ChapterRecord::getMustLabel();
		return $mustLabel[$must];
	}

	public function buildApiHeader($data)
	{
		$text = "### 请求头\n\n";
		$text = $text . $this->strLengthAdaptation('参数名称', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterRecord::TABLE_MUST_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:-', ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':---', ChapterRecord::TABLE_MUST_LENGTH) . '|' . $this->strLengthAdaptation(':---', ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':---', ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
		foreach ($data as $k => $val) {
			$key = '';
			$value = '';
			$description = '';
			$must = 0;
			if (isset($val['key'])) {
				$key = $val['key'];
			}
			if (isset($val['must'])) {
				$must = $val['must'];
			}
			if (isset($val['value'])) {
				$value = $val['value'];
			}
			if (isset($val['description'])) {
				$description = $val['description'];
			}
			$mustText = $this->getMustText($must);
			$text .= $this->strLengthAdaptation($key, ChapterRecord::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($mustText, ChapterRecord::TABLE_MUST_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterRecord::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($value, ChapterRecord::TABLE_VALUE_LENGTH) . "\n";
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
			dump($str . '-' . $length . '-' . ($defaultLength - $length));
		} else {
			$length = $lengthAll;
		}

		if ($length < $defaultLength) {
			$str = $str . str_repeat(' ', $defaultLength - $length);
		}
		return $str;
	}

	public function buildApi($data)
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
		return '- **接口说明：** ' . $description . "\n- **接口地址：** ==" . $type . '==  地址' . $value;
	}
}
