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

namespace W7\App\Model\Logic\Document\MockApi;

//返回演示数据demo
use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\Document\ChapterApiParam;
use W7\App\Model\Logic\Document\ChapterApi\ChapterRuleLogic;
use W7\Http\Message\Server\Request;

class MockApiReponseLogic
{
	public function checkRequest(Request $request, $api)
	{
		//全部数据
		$data = ChapterApiParam::query()->where('chapter_id', $api->chapter_id)
			->whereIn('location', [
				ChapterApiParam::LOCATION_REQUEST_HEADER,
				ChapterApiParam::LOCATION_REQUEST_QUERY_STRING,
				ChapterApiParam::LOCATION_REQUEST_BODY_FROM,
				ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED,
				ChapterApiParam::LOCATION_REQUEST_BODY_RAW,
			])->get();
		$msg = '';
		$jsonData = [];
		$contentType = $request->getContentType();
		$contentTypeData = explode(';', $contentType);
		if ($contentTypeData[0] == 'application/json') {
			$jsonData = $request->getBodyParams();
			$jsonData = json_decode($jsonData, true);
		}
		foreach ($data as $key => $val) {
			if ($val->enabled == ChapterApiParam::ENABLED_YES) {
				if ($val->location == ChapterApiParam::LOCATION_REQUEST_HEADER) {
					if (!$request->hasHeader($val->name)) {
						$msg .= 'header:' . $val->name . '必填 '.json_encode($request->header());
					}
				} elseif ($val->location == ChapterApiParam::LOCATION_REQUEST_QUERY_STRING) {
					if ($request->query($val->name) == null) {
						$msg .= 'query:' . $val->name . '必填 ';
					}
				} elseif ($val->location == ChapterApiParam::LOCATION_REQUEST_BODY_RAW || $jsonData) {
					if (!(isset($jsonData[$val->name]) && $jsonData[$val->name])) {
						$msg .= 'params:' . $val->name . '必填 ';
					}
				} elseif (in_array($val->location, [ChapterApiParam::LOCATION_REQUEST_BODY_FROM, ChapterApiParam::LOCATION_REQUEST_BODY_URLENCODED])) {
					if ($request->post($val->name) == null) {
						$msg .= 'params:' . $val->name . '必填 ';
					}
				}
			}
		}
		return $msg;
	}

	public function mackMockApiReponse(Request $request, $id, $route)
	{
		$urlPath = $route;
		$ChapterApi = new Document\ChapterApi();
		$Chapter = new Document\Chapter();
		$api = Document\Chapter::query()
			->where('document_id', $id)
			->leftJoin($ChapterApi->getTable(), 'chapter_id', '=', $Chapter->getTable() . '.id')
			->whereIn('url', [$urlPath, '/' . $urlPath])
			->select([$ChapterApi->getTable() . '.*'])
			->first();
		$methodLabel = $ChapterApi->methodLabel();
		if ($api && $api->method) {
			if (isset($methodLabel[$api->method])) {
				if ($methodLabel[$api->method] == $request->getMethod()) {
					//验证参数
					$checkMsg = $this->checkRequest($request, $api);
					if ($checkMsg) {
						return ['code' => 422, 'msg' => $checkMsg];
					}
					//获取rule参数样例
					$reponseId = 0;
					$ChapterApiReponse = Document\ChapterApiReponse::query()->where('chapter_id', $api->chapter_id)->get()->toArray();
					if (count($ChapterApiReponse)) {
						$reponseIds = array_column($ChapterApiReponse, 'id');
						$reponseId = $reponseIds[rand(0, count($reponseIds) - 1)];
					}

					$chapterDemoLogic = new ChapterRuleLogic($api->chapter_id);
					return $chapterDemoLogic->getChapterRuleMock($reponseId);
				} else {
					return ['code' => 401, 'msg' => '请求类型错误'];
				}
			} else {
				return ['code' => 402, 'msg' => '不支持当前请求类型：' . $request->getMethod()];
			}
		}
		return ['code' => 400, 'msg' => '请求地址不存在'];
	}
}
