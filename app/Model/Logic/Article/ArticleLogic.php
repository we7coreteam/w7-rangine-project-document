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

namespace W7\App\Model\Logic\Article;

use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\Article\Article;
use W7\App\Model\Entity\Article\ArticleColumn;
use W7\App\Model\Entity\Article\ArticleTagConfig;
use W7\App\Model\Entity\Message\Message;
use W7\App\Model\Logic\BaseLogic;
use W7\App\Model\Logic\Message\Type\RemindLogic;
use W7\Core\Helper\Traiter\InstanceTraiter;

class ArticleLogic extends BaseLogic
{
	use InstanceTraiter;

	protected $model = Article::class;

	public function getListFirstImg($list)
	{
		if (count($list['data'])) {
			$data = $list['data'];
			foreach ($data as $key => $val) {
				$data[$key]['first_img'] = $this->getContentFirstImg($val['content'], $val['home_thumbnail']);
			}
			$list['data'] = $data;
		}
		return $list;
	}

	public function getContentFirstImg($content, $homeThumbnail)
	{
		if ($homeThumbnail) {
			$pattern = "/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"]?\s.*?>/";
			preg_match_all($pattern, $content, $match);
			if (!empty($match[1][0])) {
				return $match[1][0];
			}
		}
		return null;
	}

	public function read($id, $with = '', $num = 1)
	{
		if ($with) {
			$row = Article::query()->with($with)->find($id);
		} else {
			$row = Article::query()->find($id);
		}
		$row->increment('read_num', $num);
		(new ArticleColumnLogic())->incrementNum($row, 'read_num', $num);
		return $row;
	}

	public function success($id)
	{
		$row = Article::query()->find($id);
		if ($row) {
			try {
				idb()->beginTransaction();
				if ($row->status != Article::STATUS_CREATE) {
					throw new ErrorHttpException('当前状态不是待审核状态');
				}
				$row->status = Article::STATUS_SUCCESS;
				$row->save();
				//更新栏目统计信息
				(new ArticleColumnLogic())->retry($row->column_id);
				//发送消息
				(new RemindLogic())->add(0, $row->user_id, "恭喜，您发表的文章<span class='article_title'>《{$row->title}》</span>已通过审核。", Message::REMIND_ARTICLE, $row->id);
				idb()->commit();
				return $row;
			} catch (\Exception $e) {
				idb()->rollBack();
				throw new ErrorHttpException($e->getMessage());
			}
		}
		throw new ErrorHttpException('审核失败');
	}

	public function reject($id, $reason)
	{
		$row = Article::query()->find($id);
		if ($row) {
			try {
				idb()->beginTransaction();
				if ($row->status != Article::STATUS_CREATE) {
					throw new ErrorHttpException('当前状态不是待审核状态');
				}
				$row->status = Article::STATUS_FAIL;
				$row->reason = $reason;
				$row->save();
				//更新栏目统计信息
				(new ArticleColumnLogic())->retry($row->column_id);
				//发送消息
				(new RemindLogic())->add(0, $row->user_id, "抱歉，您发表的文章<span class='article_title'>《{$row->title}》</span>审核不通过，拒绝原因：" . $reason, Message::REMIND_ARTICLE, $row->id);
				idb()->commit();
				return $row;
			} catch (\Exception $e) {
				idb()->rollBack();
				throw new ErrorHttpException($e->getMessage());
			}
		}
		throw new ErrorHttpException('驳回失败');
	}

	public function store($data)
	{
		$data = $this->checkPost($data);
		$row = parent::store($data);
		(new ArticleTagLogic())->saveTag($row);
		//更新栏目统计信息
		(new ArticleColumnLogic())->retry($row->column_id);
		return $row;
	}

	public function checkPost($data)
	{
		$column = ArticleColumn::query()->find($data['column_id']);
		if (!$column || $column->user_id != $data['user_id']) {
			throw new ErrorHttpException('专栏不存在');
		}
		if (!empty($data['tag_ids'])) {
			$tags = ArticleTagConfig::query()->whereIn('id', $data['tag_ids'])->get()->toArray();
			if (!$tags) {
				throw new ErrorHttpException('标签错误');
			}
			$tagIds = array_column($tags, 'id');
			$data['tag_ids'] = $tagIds;
		}
		return $data;
	}

	public function destroy($id, $checkData = [])
	{
		try {
			$model = $this->show($id, '', $checkData);
			$columnId = $model->column_id;
			$model->delete();
			//更新栏目统计信息
			(new ArticleColumnLogic())->retry($columnId);
			return $model;
		} catch (\Exception $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function update($id, $data, $checkData = [])
	{
		$row = $this->show($id, '', $checkData);
		$data['user_id'] = $row->user_id;
		$data = $this->checkPost($data);

		//审核失败变成待审核
		if ($row->status == Article::STATUS_FAIL) {
			$data['status'] = Article::STATUS_CREATE;
		}
		if (!$row->update($data)) {
			throw new ErrorHttpException('保存失败');
		}
		(new ArticleTagLogic())->saveTag($row);
		//更新栏目统计信息
		(new ArticleColumnLogic())->retry($row->column_id);
		return $row;
	}
}
