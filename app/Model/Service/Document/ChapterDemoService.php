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

/**
 * 数据转生成规则样例
 */

use W7\App\Model\Entity\Document\ChapterApiParam;

class ChapterDemoService extends ChapterCommonService
{
	protected $chapterId;

	public function __construct($chapterId)
	{
		$this->chapterId = $chapterId;
	}

	public function getChapterDemo($locationType)
	{
		$chapterId = $this->chapterId;
		if ($locationType == 2) {
			$locationList = array_keys($this->reponseIds());
		} else {
			$locationList = array_keys($this->requestIds());
		}
		$list = ChapterApiParam::query()->where('chapter_id', $chapterId)->where('parent_id', 0)->whereIn('location', $locationList)->get();
		return $this->getChapterDemoChildrenArray($list);
	}

	public function getChapterDemoChildrenArray($listChildren, $defaultValue = '')
	{
		if ($this->isJson($defaultValue)) {
			//如果是json
			$defaultValueList = json_decode($defaultValue, true);
		} else {
			$defaultValueList = [];
		}
		$data = [];

		$i = 0;
		foreach ($listChildren as $key => $val) {
			if ($val->default_value) {
				$defaultValue = $val->default_value;
			} else {
				if (isset($defaultValueList[$i])) {
					$defaultValue = $defaultValueList[$i];
				}
			}
			$rule = '';
			$ruleTop = '';
			if ($val->rule) {
				$ruleTop = substr($val->rule, 0, 1);
				$rule = '|' . $val->rule;
			}

			if (in_array($val->type, [ChapterApiParam::TYPE_OBJECT, ChapterApiParam::TYPE_ARRAY])) {
				//如果里面还是数组或者对象
				$listChildrenSun = ChapterApiParam::query()->where('chapter_id', $val->chapter_id)
					->where('parent_id', $val->id)->get();
				if (count($listChildrenSun) > 0) {
					if (is_numeric($val->rule) && ($val->rule > 1)) {
						//如果是多维数组
						$data[$val->name . $rule][] = $this->getChapterDemoChildrenArray($listChildrenSun, $val);
					} else {
						$data[$val->name . $rule] = $this->getChapterDemoChildrenArray($listChildrenSun, $val);
					}
				} else {
					//没有子类
					if ($this->isJson($defaultValue)) {
						//如果是json
						$defaultValueList = json_decode($defaultValue, true);
						$defaultValue = $defaultValueList[0];
					}
					if ($ruleTop == '+') {
						$data[$val->name . $rule] = $defaultValueList;
					} else {
						$data[$val->name . $rule] = $defaultValue;
					}
				}
			} else {
				if ($val->name) {
					//对象
					$data[$val->name . $rule] = $defaultValue;
				} else {
					//数字键值
					$data[] = $defaultValue;
				}
			}
		}
		return $data;
	}
}
