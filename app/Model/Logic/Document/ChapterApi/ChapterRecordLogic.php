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

use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Document\ChapterApi;
use W7\App\Model\Entity\Document\ChapterApiExtend;
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Logic\Document\ChapterApiLogic;
use W7\App\Model\Logic\Document\ChapterApiParamLogic;

/**
 * 数据存储与转markdown
 */
class ChapterRecordLogic
{
	protected $chapterId;
	protected $ids = [];
	protected $bodyParamLocation = 3;
	protected $bodyReponseLocation = 10;

	public function __construct($chapterId)
	{
		$this->chapterId = $chapterId;
	}

	/*
	 * type=1仅返回markdown2同时插入数据和返回markdown
	 * */
	public function recordToMarkdown($record, $sqlType = 2)
	{
		//markdown数据-初始化顺序
		$markdown = [
			'api' => '',
			'body' => '',
			'extend' => '',
		];
		idb()->beginTransaction();
		try {
			$chapterId = $this->chapterId;

			$cacheIndex = $this->getChapterIdRecordIndex($chapterId);
			$recordCache = icache()->get($cacheIndex);
			if ($recordCache) {
				//清除缓存
				icache()->delete($cacheIndex);
			}
			foreach ($record as $key => $val) {
				if (is_array($val)) {
					if ($key == 'api') {
						$markdown['api'] = $this->buildApi($val, $sqlType);
					} elseif ($key == 'body') {
						$body = $val;
						if (isset($record['api']['body_param_location']) && isset($body['request_body'])) {
							//指定存储body_param_location类型
							$body[$record['api']['body_param_location']] = $body['request_body'];
						} else {
							throw new ErrorHttpException('没有body_param_location或request_body');
						}
						if (isset($body['reponse_body'])) {
							//指定存储request_body类型
							$body[ChapterApiParam::LOCATION_REPONSE_BODY_RAW] = $body['reponse_body'];
						}
						$markdown['body'] = $this->buildBody($body, $sqlType);
					}
				} else {
					if ($key == 'extend') {
						$markdown['extend'] = $this->buildExtend($val, $sqlType);
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
			throw new ErrorHttpException($e->getMessage());
		}
		$markdownText = implode("\n\n", $markdown);
		return $markdownText;
	}

	public function buildExtend($data, $sqlType)
	{
		$chapterId = $this->chapterId;
		$saveData = [
			'chapter_id' => $chapterId,
			'extend' => $data,
		];
		if ($sqlType == 2) {
			$chapterApiExtend = ChapterApiExtend::query()->where('chapter_id', $chapterId)->first();
			if ($chapterApiExtend) {
				$chapterApiExtend->update($saveData);
			} else {
				ChapterApiExtend::query()->create($saveData);
			}
		}
		return $data;
	}

	//文档排序
	public function bodySort($data)
	{
		//按getLocationLabel 顺序排序返回生成markdown
		$newData = [];
		$locationLabel = ChapterApiParamLogic::instance()->getLocationLabel();
		foreach ($locationLabel as $key => $val) {
			if (isset($data[$key])) {
				$newData[$key] = $data[$key];
			}
		}
		return $newData;
	}

	public function buildBody($data, $sqlType)
	{
		//初始化顺序
		$data = $this->bodySort($data);
		$text = '';
		foreach ($data as $k => $v) {
			if (in_array($k, [ChapterApiParam::LOCATION_REQUEST_HEADER, ChapterApiParam::LOCATION_REPONSE_HEADER])) {
				$text .= $this->buildApiBody($k, $v, $sqlType);
			} elseif (in_array($k, [ChapterApiParam::LOCATION_REQUEST_QUERY_PATH, ChapterApiParam::LOCATION_REQUEST_QUERY_STRING])) {
				$text .= $this->buildApiBody($k, $v, $sqlType);
			} elseif ($k == $this->bodyParamLocation) {
				$text .= $this->buildApiBody($k, $v, $sqlType);
			} elseif ($k == $this->bodyReponseLocation) {
				$text .= $this->buildApiBody($k, $v, $sqlType);
			}
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

	public function bodyTableTop()
	{
		$text = $this->strLengthAdaptation('参数名称', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('类型', ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
		$text = $text . $this->strLengthAdaptation('|:-', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
		return $text;
	}

	public function getLocatinonText($location)
	{
		$locationLabel = ChapterApiParamLogic::instance()->getLocationLabel();
		if (isset($locationLabel[$location])) {
			return $locationLabel[$location];
		}
		throw new ErrorHttpException('响应类型错误');
	}

	public function getEnabledText($enabled)
	{
		$enabledLabel = ChapterApiParamLogic::instance()->getEnabledLabel();
		if (isset($enabledLabel[$enabled])) {
			return $enabledLabel[$enabled];
		}
		throw new ErrorHttpException('必填类型错误');
	}

	public function buildApi($data, $sqlType)
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

		$methodLabel = ChapterApiLogic::instance()->getMethodLabel();
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
			$bodyParamLocationList = [
				ChapterApiParam::LOCATION_REQUEST_QUERY_STRING => 'Request.Query.String',
				ChapterApiParam::LOCATION_REQUEST_BODY_FROM => 'Request.Body.form-data',
				ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED => 'Request.Body.urlencoded',
				ChapterApiParam::LOCATION_REQUEST_BODY_RAW => 'Request.Body.raw',
			];
			if (in_array($data['body_param_location'], array_keys($bodyParamLocationList))) {
				$bodyParamLocation = $data['body_param_location'];
				$this->bodyParamLocation = $bodyParamLocation;
			}
		}
		if (isset($data['status_code'])) {
			$statusCode = $data['status_code'];
			$statusCodeLists = ChapterApiLogic::instance()->getStatusCode();
			if (!in_array($statusCode, $statusCodeLists)) {
				throw new ErrorHttpException('状态码错误');
			}
		}

		$text = '- **请求方式：** ' . $methodLabel[$method] . "\n- **接口地址：** " . $url . "\n";
		if ($statusCode) {
			$text .= '- **状态码：** ' . $statusCode . "\n";
		}
		if ($description) {
			$text .= '- **接口说明：** ' . $description . "\n";
		}
		//存储
		$saveData = [
			'chapter_id' => $chapterId,
			'url' => $url,
			'method' => $method,
			'description' => $description,
			'body_param_location' => $bodyParamLocation
		];
		if ($sqlType == 2) {
			$chapterApi = ChapterApi::query()->where('chapter_id', $chapterId)->first();
			if ($chapterApi) {
				$chapterApi->update($saveData);
			} else {
				ChapterApi::query()->create($saveData);
			}
		}
		return $text;
	}

	public function buildApiBody($location, $data, $sqlType)
	{
		$text = '';
		if ($data && is_array($data)) {
			$title = $this->getLocatinonText($location);
			$textTop = '### ' . $title . "\n\n";
			$textTop = $textTop . $this->bodyTableTop();
			foreach ($data as $k => $val) {
				$text .= $this->buildBodyChildren($location, $val, 0, 0, $sqlType);
			}
			if ($text) {
				$text = $textTop . $text;
			}
		}
		return $text;
	}

	public function getTypeText($type)
	{
		$typeLabel = ChapterApiParamLogic::instance()->getTypeLabel();
		if (isset($typeLabel[$type])) {
			return $typeLabel[$type];
		}
		throw new ErrorHttpException('参数类型错误');
	}

	public function getChildrenTop($level)
	{
		return str_repeat('&emsp;', $level);
	}

	public function buildBodyChildren($location, $data, $level = 0, $parentId = 0, $sqlType = 2)
	{
		$childrenTop = $this->getChildrenTop($level);
		$name = '';
		$type = 1;
		$defaultValue = '';
		$description = '';

		$enabled = 1;
		if (isset($data['name'])) {
			$name = $data['name'];
		}
		if (isset($data['type']) && $data['type']) {
			$type = $data['type'];
		}
		if (isset($data['enabled']) && $data['enabled']) {
			$enabled = $data['enabled'];
		}
		if (isset($data['default_value'])) {
			$defaultValue = $data['default_value'];
			if (is_array($defaultValue)) {
				//如果默认值是个数组
				$defaultValue = json_encode($defaultValue, JSON_UNESCAPED_UNICODE);
			}
		}
		if (isset($data['description'])) {
			$description = $data['description'];
		}

		$enabledText = $this->getEnabledText($enabled);
		$typeText = $this->getTypeText($type);
		if ($name || $description) {
			if (!$name) {
				$name = ' ';
			}
			$text = $this->strLengthAdaptation($childrenTop . $name, ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($typeText, ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation($enabledText, ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($defaultValue, ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
			//存储
			if ($sqlType == 2) {
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
					'default_value' => $defaultValue,
				];

				$id = $parentId;

				$hasRow = 0;
				if (isset($data['id']) && $data['id']) {
					$id = $data['id'];
					$chapterApiParam = ChapterApiParam::query()->find($data['id']);
					if ($chapterApiParam && $chapterApiParam->chapter_id == $chapterId && $chapterApiParam->location == $location) {
						$chapterApiParam->update($saveData);
						$hasRow = 1;
						$ids[count($ids)] = $data['id'];
					}
				}
				if ($hasRow == 0) {
					$chapterApiParam = ChapterApiParam::query()->create($saveData);
					if ($chapterApiParam) {
						$ids[count($ids)] = $chapterApiParam->id;
						$id = $chapterApiParam->id;
					}
				}
				$this->ids = $ids;
			}
		}

		if (isset($data['children']) && (!empty($data['children'])) && is_array($data['children'])) {
			foreach ($data['children'] as $k => $val) {
				$text .= $this->buildBodyChildren($location, $val, $level + 1, $id, $sqlType = 2);
			}
		}
		return $text;
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

	public function chapterApiParamData($chapterId)
	{
		//全部数据
		$chapterApiParam = ChapterApiParam::query()->where('chapter_id', $chapterId)->get();
		return $chapterApiParam;
	}

	public function showRecord()
	{
		$chapterId = $this->chapterId;

		$cacheIndex = $this->getChapterIdRecordIndex($chapterId);
		$recordCache = icache()->get($cacheIndex);
		if ($recordCache) {
			return json_decode($recordCache, true);
		}

		$record = [
			'api' => [],
			'body' => [
				'1' => [],
				'2' => [],
				'request_body' => [],
				'reponse_body' => [],
			],
			'extend' => ''
		];
		$body = [];

		$chapterApi = ChapterApi::query()->where('chapter_id', $chapterId)->first();
		if ($chapterApi) {
			$chapterApiParamData = $this->chapterApiParamData($chapterId);
			if ($chapterApiParamData) {
				foreach ($chapterApiParamData as $key => $val) {
					if ($val->parent_id == 0) {
						$val->children = $this->getBodyChildren($chapterApiParamData, $val->id);
						if ($val->location == $chapterApi->body_param_location) {
							//如果当前列是request_body
							$record['body']['request_body'][] = $val->toArray();
						} elseif ($val->location == ChapterApiParam::LOCATION_REPONSE_BODY_RAW) {
							//如果当前列是reponse_body
							$record['body']['reponse_body'][] = $val->toArray();
						} else {
							$record['body'][$val->location][] = $val->toArray();
						}
					}
				}
			}
			$chapterApiExtend = ChapterApiExtend::query()->where('chapter_id', $chapterId)->first();
			if ($chapterApiExtend) {
				$record['extend'] = $chapterApiExtend->extend;
			}
			//返回tab_location
			$tab_location = 1;
			if ($record['body']['1']) {
				$tab_location = 1;
			} elseif ($record['body']['2']) {
				$tab_location = 2;
			} elseif ($record['body']['request_body']) {
				$tab_location = 3;
			}
			$chapterApi->tab_location = $tab_location;
			$record['api'] = $chapterApi->toArray();
		}
		icache()->set($cacheIndex, json_encode($record), 3600 * 24);
		return $record;
	}

	public function getChapterIdRecordIndex($chapterId)
	{
		return 'ChapterIdRecordIndex:' . $chapterId;
	}

	public function getBodyChildren($chapterApiParamData, $parentId)
	{
		$chapterApiParam = [];
		if ($chapterApiParamData) {
			foreach ($chapterApiParamData as $key => $val) {
				if ($val->parent_id == $parentId) {
					$val->children = $this->getBodyChildren($chapterApiParamData, $val->id);
					$chapterApiParam[] = $val;
				}
			}
			return $chapterApiParam;
		}
		return [];
	}
}
