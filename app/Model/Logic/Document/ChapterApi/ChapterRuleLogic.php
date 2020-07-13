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

use W7\App\Model\Entity\Document\ChapterApiParam;

//返回演示数据demo
class ChapterRuleLogic extends ChapterCommonLogic
{
	protected $chapterId;

	public function __construct($chapterId)
	{
		$this->chapterId = $chapterId;
	}

	public function getChapterRuleMock($locationType, $reponseId)
	{
		$chapterId = $this->chapterId;
		if ($locationType == 2) {
			$locationList = array_keys($this->reponseIds());
		} else {
			$locationList = array_keys($this->requestIds());
		}
		$obj = ChapterApiParam::query()->where('chapter_id', $chapterId);
		if ($locationType && $reponseId) {
			$obj->where('reponse_id', $reponseId);
		}
		$data= $obj->whereIn('location', $locationList)->get()->toArray();
		$url=ienv('MOCK_API');
		$json=$this->send_post($url,json_encode($data));
		return json_decode($json);
	}

	public function send_post($url, $json) {

		$postdata = $json;
		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type:application/json',
				'content' => $postdata,
				'timeout' => 15 * 60 // 超时时间（单位:s）
			)
		);
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		return $result;
	}

	public function getChapterIdRequestIndex()
	{
		return 'ChapterIdRequestIndexV1:' . $this->chapterId;
	}

	public function getChapterIdReponseIndex($reponseId)
	{
		return 'ChapterIdReponseIndexV1:' . $this->chapterId . '-' . $reponseId;
	}

	public function getChapterRule($locationType, $reponseId = 0)
	{
		$chapterId = $this->chapterId;

		if ($locationType == 2) {
			$locationList = array_keys($this->reponseIds());
			$cacheIndex = $this->getChapterIdReponseIndex($reponseId);
		} else {
			$cacheIndex = $this->getChapterIdRequestIndex();
			$locationList = array_keys($this->requestIds());
		}
		$Cache = icache()->get($cacheIndex);
		if ($Cache) {
			return json_decode($Cache, true);
		}

		$obj = ChapterApiParam::query()->where('chapter_id', $chapterId);
		if ($locationType && $reponseId) {
			$obj->where('reponse_id', $reponseId);
		}
		$chapterList = $obj->whereIn('location', $locationList)->get();
		$data = $this->getChapterDemoChildrenArray($chapterList, '', 0);
		icache()->set($cacheIndex, json_encode($data['rule']), 3600 * 24);
		return $data['rule'];
	}

	//导出-2键值对字符串，3键值对数组
	public function getArrayToKeyValue($data, $type)
	{
		$str = http_build_query($data);
		if ($type == 3) {
			//键值对数组
			$info1 = explode('&', $str);
			$reply = [];
			foreach ($info1 as $key => $val) {
				$info2 = explode('=', $val);
				$name = urldecode($info2[0]);
				$value = urldecode(str_replace($info2[0] . '=', '', $val));
				$reply[$name] = $value;
			}
			return $reply;
		} else {
			//纯键值对
			$str = str_replace('&', "\n", $str);
			$str = str_replace('=', ':', $str);
			return urldecode($str);
		}
	}

	public function getChapterDemoChildrenArray($listChildren, $defaultValue = '', $parentId = 0)
	{
		if ($this->isJson($defaultValue)) {
			//如果是json
			$defaultValueList = json_decode($defaultValue, true);
		} else {
			$defaultValueList = [];
		}
		$data = [];
		$rule = [];
		$descriptionData = [];
		$i = 0;

		foreach ($listChildren as $key => $val) {
			if ($val->parent_id == $parentId) {
				$ruleName = $val->name;
				if ($val->rule) {
					$ruleName = $val->name . '|' . $val->rule;
				}

				$defaultValue = '';
				if ($val->default_value) {
					$defaultValue = $val->default_value;
				} else {
					if (isset($defaultValueList[$i])) {
						$defaultValue = $defaultValueList[$i];
					}
				}
				$description = $val->description;

				if (in_array($val->type, [ChapterApiParam::TYPE_OBJECT, ChapterApiParam::TYPE_ARRAY])) {
					//如果里面还是数组或者对象
					if ($val->type == ChapterApiParam::TYPE_ARRAY) {
						if (is_numeric($val->rule) && ($val->rule > 0)) {
							//如果是多维数组
							$chapterDemoChildren = $this->getChapterDemoChildrenArray($listChildren, '', $val->id);
							$data[$val->name][] = $chapterDemoChildren['data'];
							$rule[$ruleName][] = $chapterDemoChildren['rule'];
							$descriptionData[$val->name][] = $chapterDemoChildren['descriptionData'];
						} else {
							$chapterDemoChildren = $this->getChapterDemoChildrenArray($listChildren, '', $val->id);
							$data[$val->name] = $chapterDemoChildren['data'];
							$rule[$ruleName] = $chapterDemoChildren['rule'];
							$descriptionData[$val->name] = $chapterDemoChildren['descriptionData'];
						}
					} elseif ($val->type == ChapterApiParam::TYPE_OBJECT) {
						$chapterDemoChildren = $this->getChapterDemoChildrenArray($listChildren, '', $val->id);
						$data[$val->name] = $chapterDemoChildren['data'];
						$rule[$ruleName] = $chapterDemoChildren['rule'];
						$descriptionData[$val->name] = $chapterDemoChildren['descriptionData'];
					} else {
						//没有子类
						if ($this->isJson($defaultValue)) {
							//如果是json
							$defaultValueList = json_decode($defaultValue, true);
							$defaultValue = $defaultValueList[0];
						}
						$data[$val->name] = $defaultValue;
						$rule[$ruleName] = $defaultValue;
						$descriptionData[$val->name] = $description;
					}
				} else {
					if ($val->name) {
						//对象
						$data[$val->name] = $defaultValue;
						$rule[$ruleName] = $defaultValue;
						$descriptionData[$val->name] = $description;
					} else {
						//数字键值
						$data[] = $defaultValue;
						$rule[$ruleName] = $defaultValue;
						$descriptionData[] = $description;
					}
				}
			}
		}
		return ['rule' => $rule, 'data' => $data, 'descriptionData' => $descriptionData];
	}
}
