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

namespace W7\App\Model\Logic;

use W7\App\Model\Entity\Message\Message;
use W7\App\Model\Entity\Video;
use W7\App\Model\Entity\Video\Category;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\Video\CategoryLogic;
use W7\App\Model\Logic\Message\Type\RemindLogic;
use W7\App\Model\Service\Qcloud\QcloudVodService;

class VideoLogic extends BaseLogic
{
	protected $model = Video::class;

	public function homeData()
	{
		$carousel = Video\Carousel::query()->get();
		$activity = Video\Activity::query()->get();
		$videoRank = Video::query()->where('status', Video::STATUS_SUCCESS)->with(['category', 'category.categoryConfig', 'user'])->orderBy('play_num', 'desc')->limit(10)->get();
		return [
			'carousel' => $carousel,
			'activity' => $activity,
			'videoRank' => $videoRank
		];
	}

	public function recommendVideo($videoId)
	{
		$videos = Video::query()
			->leftJoin('video_category', 'video.id', 'video_category.video_id')
			->whereIn('video_category.category_id', function ($query) use ($videoId) {
				$query->select('category_id')->from('video_category')->where('video_id', $videoId)->get();
			})
			->where('video.id', '<>', $videoId)
			->where('video.status', Video::STATUS_SUCCESS)
			->with(['user'])
			->limit(6)
			->inRandomOrder()
			->get(['video.*']);

		return $videos;
	}

	public function indexHot()
	{
		$videos = Video::query()->where('status', Video::STATUS_SUCCESS)->with(['category', 'category.categoryConfig', 'user'])->orderBy('play_num', 'desc')->limit(50)->get()->toArray();
		if (count($videos) < 6) {
			$hotVideos = $videos;
		} else {
			$keys = array_rand($videos, 6);
			$hotVideos = [];
			foreach ($keys as $key) {
				$hotVideos[] = $videos[$key];
			}
		}
		return $hotVideos;
	}

	public function store($data)
	{
		$data = $this->checkPost($data);
		$data['status'] = Video::STATUS_CREATE;
		$row = parent::store($data);
		CategoryLogic::instance()->saveCategory($row);
		return $row;
	}

	public function update($id, $data, $checkData = [])
	{
		$row = $this->show($id, '', $checkData);
		$data['user_id'] = $row->user_id;
		$data = $this->checkPost($data);
		$data['status'] = Video::STATUS_CREATE;
		if (!$row->update($data)) {
			throw new ErrorHttpException('保存失败');
		}
		CategoryLogic::instance()->saveCategory($row);
		return $row;
	}

	public function destroy($id, $checkData = [])
	{
		idb()->beginTransaction();
		try {
			$model = $this->show($id, '', $checkData);
			$model->comment()->delete();
			$model->praise()->delete();
			$model->category()->delete();
			$model->delete();
			idb()->commit();
			return $model;
		} catch (\Exception $e) {
			idb()->rollBack();
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function show($id, $with = '', $checkData = [])
	{
		$row = parent::show($id, $with, $checkData);
		$row->url = (new QcloudVodService())->makeVodDownloadSign($row->url);
		return $row;
	}

	public function checkPost($data)
	{
		if (!empty($data['category_id'])) {
			$category = Category::query()->whereIn('id', $data['category_id'])->get()->toArray();
			if (!$category) {
				throw new ErrorHttpException('分类错误');
			}
			$data['category_id'] = array_column($category, 'id');
		}
		return $data;
	}

	public function success($id)
	{
		$row = Video::query()->find($id);
		if ($row) {
			if ($row->status != Video::STATUS_CREATE) {
				throw new ErrorHttpException('当前状态不是待审核状态');
			}
			$row->status = Video::STATUS_SUCCESS;
			$row->save();
			(new RemindLogic())->add(0, $row->user_id, "恭喜，您发表的视频<span class='article_title'>《{$row->title}》</span>已通过审核。", Message::REMIND_VIDEO, $row->id);
			return $row;
		} else {
			throw new ErrorHttpException('审核失败');
		}
	}

	public function reject($id, $reason)
	{
		$row = Video::query()->find($id);
		if ($row) {
			if ($row->status != Video::STATUS_CREATE) {
				throw new ErrorHttpException('当前状态不是待审核状态');
			}
			$row->status = Video::STATUS_FAIL;
			$row->reason = $reason;
			$row->save();
			(new RemindLogic())->add(0, $row->user_id, "抱歉，您发表的视频<span class='article_title'>《{$row->title}》</span>审核不通过，拒绝原因：" . $reason, Message::REMIND_VIDEO, $row->id);
			return $row;
		} else {
			throw new ErrorHttpException('审核失败');
		}
	}

	public function addPlayNum($videoId)
	{
		$video = Video::query()->find($videoId);
		if (!$video) {
			throw new ErrorHttpException('视频不存在');
		}
		$video->increment('play_num', 1);
		return $video;
	}
}
