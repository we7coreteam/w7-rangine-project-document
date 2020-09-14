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

namespace W7\App\Command\Todo;

use W7\App\Model\Entity\Document;
use W7\Console\Command\CommandAbstract;

class CustomCommand extends CommandAbstract
{
	protected $description = '上线需要执行的脚本';

	protected function configure()
	{
		$this->setName('todo:custom');
	}

	protected function handle($options)
	{
		$this->updateDocumentNull();
	}

	public function updateDocumentNull()
	{
		$list = Document::query()
			->leftJoin('document_chapter', 'document_chapter.document_id', 'document.id')
			->select(['document.id', 'document.name', 'document_chapter.parent_id'])
			->whereNull('document_chapter.id')
			->get();
		foreach ($list as $key => $val) {
			if ($val->id) {
				$l = Document\Chapter::query()->where('document_id', $val->id)->get();
				if (!count($l)) {
					$val->delete();
					$this->output->writeln($val->id . '空项目' . $val->name . '已处理');
				}
			}
		}
		$this->output->writeln('已处理结束');
	}
}
