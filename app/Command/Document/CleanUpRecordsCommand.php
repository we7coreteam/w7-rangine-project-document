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

namespace W7\App\Command\Document;

use W7\App\Model\Entity\Document\ChapterContent;
use W7\Console\Command\CommandAbstract;

class CleanUpRecordsCommand extends CommandAbstract
{
	protected $description = '清理API文档缓存';

	protected function configure()
	{
		$this->setName('todo:clean_up_records');
	}

	protected function handle($options)
	{
		go(function () {
			$this->clean();
		});
	}

	/*
	 * 本操作用于，更改API文档markdown结构，手动清理缓存生效
	 * */
	public function clean()
	{
		try {
			ChapterContent::query()->where('layout', 1)->update(['content' => '']);
			$this->output->success('API文档缓存已清理');
		} catch (\Exception $e) {
			$this->output->error($e->getMessage());
		}
	}
}
