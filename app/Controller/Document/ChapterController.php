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

namespace W7\App\Controller\Document;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Star;
use W7\App\Model\Entity\UserOperateLog;
use W7\App\Model\Logic\ChapterLogic;
use W7\App\Model\Logic\DocumentLogic;
use W7\App\Model\Logic\UserLogic;
use W7\App\Model\Logic\UserOperateLogic;
use W7\App\Model\Logic\UserShareLogic;
use W7\Http\Message\Server\Request;

class ChapterController extends BaseController
{
	/**
	 * 某一个文档的目录
	 * @param Request $request
	 * @return array
	 */
	public function catalog(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法'
		]);

		try {
			$result = ChapterLogic::instance()->getCatalog($params['document_id']);

			$user = $request->getAttribute('user');
			if (empty($user->isReader)) {
				throw new ErrorHttpException('无权限阅读该文档');
			}
			if ($user && !empty($user->id)) {
				UserOperateLog::query()->create([
					'user_id' => $user->id,
					'document_id' => $params['document_id'],
					'chapter_id' => 0,
					'operate' => UserOperateLog::PREVIEW,
					'remark' => $user->username . '阅读文档'
				]);
			}
			return $this->data($result);
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function detail(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required|integer|min:1',
			'chapter_id' => 'required|integer|min:1'
		], [
			'document_id.required' => '文档id必填',
			'document_id.integer' => '文档id非法',
			'chapter_id.required' => '章节id必填',
			'chapter_id.integer' => '章节id非法'
		]);
		$shareKey = $request->post('share_key');

		try {
			$shareInfo = [];
			if ($shareKey) {
				$shareInfo = UserShareLogic::instance()->getUidAndChapterByShareKey($shareKey);
			}
			$chapter = ChapterLogic::instance()->getById($params['chapter_id'], $params['document_id']);

			$user = $request->getAttribute('user');
			if (empty($user->isReader)) {
				throw new ErrorHttpException('无权限阅读该文档');
			}
			if (!empty($user->id)) {
				UserOperateLog::query()->create([
					'user_id' => $user->id,
					'document_id' => $params['document_id'],
					'chapter_id' => $params['chapter_id'],
					'operate' => UserOperateLog::PREVIEW,
					'remark' => $user->username . '浏览章节' . $chapter->name
				]);
				//如果当前用户不是分享者并且是当前章节时，添加分享记录
				if ($shareInfo && $shareInfo[0] != $user->id && $shareInfo[1] == $params['chapter_id']) {
					if (!UserOperateLog::query()->where('user_id', '=', $shareInfo[0])->where('target_user_id', '=', $user->id)->where('chapter_id', '=', $params['chapter_id'])->exists()) {
						$sharerUser = UserLogic::instance()->getByUid($shareInfo[0]);
						UserOperateLog::query()->create([
							'user_id' => $shareInfo[0],
							'document_id' => $params['document_id'],
							'chapter_id' => $params['chapter_id'],
							'target_user_id' => $user->id,
							'operate' => UserOperateLog::SHARE,
							'remark' => $sharerUser->username . '分享链接' . UserShareLogic::instance()->getShareUrl($shareInfo[0], $params['document_id'], $params['chapter_id']) . '给' . $user->username
						]);
					}
				}
			}
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}

		if (!$chapter) {
			throw new ErrorHttpException('该章节不存在！');
		}

		$document = DocumentLogic::instance()->getById($params['document_id']);
		$creator = UserOperateLogic::instance()->getByChapterAndOperate($chapter->id, UserOperateLog::CREATE);
		if ($creator) {
			$author = $creator->user;
		} else {
			$author = $document->user;
		}

		if (!empty($user->id)) {
			$star = Star::query()->where('user_id', '=', $user->id)->where('chapter_id', '=', $chapter->id)->first();
		}
		$result = [
			'id' => $chapter->id,
			'parent_id' => $chapter->parent_id,
			'name' => $chapter->name,
			'document_id' => $chapter->document_id,
			'created_at' => $chapter->created_at->toDateTimeString(),
			'updated_at' => $chapter->updated_at->toDateTimeString(),
			'content' => $chapter->content->content,
			'star_id' => !empty($star) ? $star->id : '',
			'prev_item' => [
				'id' => $chapter->prevItem->id ?? '',
				'name' => $chapter->prevItem->name ?? '',
			],
			'next_item' => [
				'id' => $chapter->nextItem->id ?? '',
				'name' => $chapter->nextItem->name ?? '',
			],
			'author' => [
				'uid' => $author->id,
				'username' => $author->username,
			]
		];

		return $this->data($result);
	}

	public function search(Request $request)
	{
		$this->validate($request, [
			'keywords' => 'required',
		], [
			'keywords.required' => '关键字必填',
		]);

		$keyword = $request->input('keywords');
		$documentId = intval($request->input('document_id'));

	}
}
