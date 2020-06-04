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
use W7\App\Model\Entity\Document\ChapterApiReponse;
use W7\App\Model\Logic\Document\ChapterApiLogic;
use W7\App\Model\Logic\Document\ChapterApiParamLogic;
use function GuzzleHttp\Psr7\build_query;

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
			'reponse' => '',
			'extend' => '',
		];
		$api = '';
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
						$api = $this->buildApi($val, $sqlType);
					} elseif ($key == 'body') {
						$body = $val;
						if (isset($record['api']['body_param_location']) && isset($body['request_body'])) {
							//指定存储body_param_location类型
							$body[$record['api']['body_param_location']] = $body['request_body'];
						} else {
							if ($sqlType == 2) {
								throw new ErrorHttpException('没有body_param_location或request_body');
							}
						}
						if (isset($body['reponse_body'])) {
							//指定存储request_body类型-兼容老版本，先不删除
							$body[ChapterApiParam::LOCATION_REPONSE_BODY_RAW] = $body['reponse_body'];
						}
						$markdown['body'] = $this->buildBody($body, $sqlType);
					} elseif ($key == 'reponse') {
						$reponse = $val;
						$markdown['reponse'] = $this->buildReponse($reponse, $sqlType);
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
			} else {
				ChapterApiParam::query()->where('chapter_id', $chapterId)->delete();
			}
			if ($api) {
				$markdown['api'] = $this->buildApiText($api, $chapterId);
			}
			//替换API的URL
			idb()->commit();
		} catch (\Throwable $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
		$markdownText = implode("\n\n", $markdown);
		return $markdownText;
	}

	public function buildReponse($reponse, $sqlType)
	{
		$text = '';
		$reponseIds = [];
		$chapterId = $this->chapterId;
		if ($reponse) {
			foreach ($reponse as $key => $val) {
				if ($val['id']) {
					//修改
					$chapterApiReponse = ChapterApiReponse::query()->find($val['id']);
					if ($chapterApiReponse) {
						$chapterApiReponse->description = $val['description'];
						$chapterApiReponse->save();
					}
				} else {
					//新增
					$chapterApiReponse = ChapterApiReponse::query()->create([
						'chapter_id' => $chapterId,
						'description' => $val['description']
					]);
				}
				if ($chapterApiReponse && $val['data']) {
					$reponseIds[count($reponseIds)] = $chapterApiReponse->id;
					$text .= '### 响应：' . $val['description'] . "\n";
					$text .= $this->buildApiBody(ChapterApiParam::LOCATION_REPONSE_BODY_RAW, $val['data'], $sqlType, $chapterApiReponse);
				}
			}
			if ($reponseIds) {
				ChapterApiReponse::query()->where('chapter_id', $chapterId)->whereNotIn('id', $reponseIds)->delete();
			} else {
				ChapterApiReponse::query()->where('chapter_id', $chapterId)->delete();
			}
		}
		return $text;
	}

	public function buildBody($data, $sqlType, $chapterApiReponse = '')
	{
		if(!$data){
			//没有数据
			return '';
		}
		//初始化顺序
		$data = $this->bodySort($data);
		$text = '';
		$hasRequest = 0;
		$hasReponse = 0;
		$requestTop = "### 请求\n";
		$reponseTop = "### 响应\n";
		foreach ($data as $k => $v) {
			if (in_array($k, [$this->bodyReponseLocation, ChapterApiParam::LOCATION_REPONSE_HEADER])) {
				if (!$hasReponse && $v) {
					$text .= $reponseTop;
					$hasReponse = 1;
				}
				$text .= $this->buildApiBody($k, $v, $sqlType, $chapterApiReponse);
			} elseif (in_array($k, [$this->bodyParamLocation, ChapterApiParam::LOCATION_REQUEST_HEADER, ChapterApiParam::LOCATION_REQUEST_QUERY_PATH, ChapterApiParam::LOCATION_REQUEST_QUERY_STRING])) {
				//请求
				if (!$hasRequest && $v) {
					$text .= $requestTop;
					$hasRequest = 1;
				}
				$text .= $this->buildApiBody($k, $v, $sqlType);
			}
		}
		return $text;
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

	public function strLengthAdaptation($str, $defaultLength = 20)
	{
		if ($str === null) {
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

	public function bodyTableTop($chapterApiReponse)
	{
		if ($chapterApiReponse) {
			$text = $this->strLengthAdaptation('参数名称', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('类型', ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
			$text = $text . $this->strLengthAdaptation('|:-', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
		} else {
			$text = $this->strLengthAdaptation('参数名称', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation('类型', ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation('必填', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation('描述', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation('示例值', ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
			$text = $text . $this->strLengthAdaptation('|:-', ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation(':-:', ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation(':-', ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
		}
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
				ChapterApiParam::LOCATION_REQUEST_BODY_BINARY => 'Request.Body.binary',
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

		//存储
		if ($sqlType == 2) {
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
		}
		return [
			'methodLabel' => $methodLabel[$method],
			'url' => $url,
			'description' => $description
		];
	}

	public function buildApiText($data, $chapterId)
	{
		$url = $data['url'];
		//获取QUERY参数样例
		$chapterDemoLogic = new ChapterDemoLogic($chapterId);
		$query = $chapterDemoLogic->getChapterDemo(0, 1, [ChapterApiParam::LOCATION_REQUEST_QUERY_STRING]);
		if (isset($query['data']) && $query['data']) {
			$urlStr = build_query($query['data']);
			$url = $url . '?' . $urlStr;
		}
		$text = '> ' . $data['methodLabel'] . ' /' . $url . "\n\n";
		if ($data['description']) {
			$text .= $data['description'] . "\n";
		}
		return $text;
	}

	public function buildApiBody($location, $data, $sqlType, $chapterApiReponse = '')
	{
		$text = '';
		if ($data && is_array($data)) {
			$title = $this->getLocatinonText($location);
			$textTop = '### ' . $title . "\n\n";
			$textTop = $textTop . $this->bodyTableTop($chapterApiReponse);
			foreach ($data as $k => $val) {
				$text .= $this->buildBodyChildren($location, $val, 0, 0, $sqlType, $chapterApiReponse);
			}
			if ($text) {
				$text = $textTop . $text;
			}
			$text .= "\n\n";
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

	public function buildBodyChildren($location, $data, $level = 0, $parentId = 0, $sqlType = 2, $chapterApiReponse = '')
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

			$textName = $name;
			if ($type == ChapterApiParam::TYPE_ARRAY) {
				$textName .= '[]';
			} elseif ($type == ChapterApiParam::TYPE_OBJECT) {
				$textName .= '{}';
			}
			if ($chapterApiReponse) {
				//响应不返回是否必填
				$text = $this->strLengthAdaptation($childrenTop . $textName, ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($typeText, ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($defaultValue, ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
			} else {
				$text = $this->strLengthAdaptation($childrenTop . $textName, ChapterApiParam::TABLE_NAME_LENGTH) . '|' . $this->strLengthAdaptation($typeText, ChapterApiParam::TABLE_TYPE_LENGTH) . '|' . $this->strLengthAdaptation($enabledText, ChapterApiParam::TABLE_ENABLED_LENGTH) . '|' . $this->strLengthAdaptation($description, ChapterApiParam::TABLE_DESCRIPTION_LENGTH) . '|' . $this->strLengthAdaptation($defaultValue, ChapterApiParam::TABLE_VALUE_LENGTH) . "\n";
			}

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
				if ($chapterApiReponse) {
					$saveData['reponse_id'] = $chapterApiReponse->id;
				}

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
				$text .= $this->buildBodyChildren($location, $val, $level + 1, $id, $sqlType = 2, $chapterApiReponse);
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

	public function chapterApiParamData($chapterId, $location, $reponseId = 0)
	{
		//全部数据
		return ChapterApiParam::query()
			->where('location', $location)->where('chapter_id', $chapterId)->where('reponse_id', $reponseId)->get();
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
			'api' => '',
			'body' => [
				'1' => [],
				'2' => [],
				'request_body' => []
			],
			'reponse' => [],
			'extend' => ''
		];
		$body = [];

		$chapterApi = ChapterApi::query()->where('chapter_id', $chapterId)->first();
		if ($chapterApi) {
			$record['body'] = $this->getBody($chapterId, $chapterApi);
			$record['reponse'] = $this->getReponse($chapterId);
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
			$record['api'] = $chapterApi;
		}
		icache()->set($cacheIndex, json_encode($record), 3600 * 24);
		return $record;
	}

	public function getBodyInfo($chapterId, $location, $reponseId = 0)
	{
		$data = [];
		$chapterApiParamData = $this->chapterApiParamData($chapterId, $location, $reponseId);

		if ($chapterApiParamData) {
			foreach ($chapterApiParamData as $key => $val) {
				if ($val->parent_id == 0) {
					$val->children = $this->getBodyChildren($chapterApiParamData, $val->id);
					$data[] = $val->toArray();
				}
			}
		}
		return $data;
	}

	public function getReponse($chapterId)
	{
		$data = [];
		$list = ChapterApiReponse::query()->where('chapter_id', $chapterId)->get()->toArray();
		if (count($list)) {
			//兼容之前的
			foreach ($list as $key => $val) {
				$data[] = [
					'id' => $val['id'],
					'chapter_id' => $chapterId,
					'description' => $val['description'],
					'data' => $this->getBodyInfo($chapterId, ChapterApiParam::LOCATION_REPONSE_BODY_RAW, $val['id'])
				];
			}
			//删除旧的
		} else {
			//兼容之前的
			$data[] = [
				'id' => 0,
				'chapter_id' => $chapterId,
				'description' => '',
				'data' => $this->getBodyInfo($chapterId, ChapterApiParam::LOCATION_REPONSE_BODY_RAW)
			];
		}
		return $data;
	}

	public function getBody($chapterId, $chapterApi)
	{
		$body = [
			'1' => $this->getBodyInfo($chapterId, 1),
			'2' => $this->getBodyInfo($chapterId, 2),
			'request_body' => $this->getBodyInfo($chapterId, $chapterApi->body_param_location)
		];
		return $body;
	}

	public function getChapterIdRecordIndex($chapterId)
	{
		return 'ChapterIdRecordIndexV1:' . $chapterId;
	}

	public function getBodyChildren($chapterApiParamData, $parentId)
	{
		$chapterApiParam = [];
		if (count($chapterApiParamData)) {
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
