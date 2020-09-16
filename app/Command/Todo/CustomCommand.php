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
use W7\App\Model\Entity\UserThirdParty;
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
		$this->updateDoc();
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
					$this->output->writeln($val->id . '空项目《' . $val->name . '》已处理');
				}
			}
		}
		$this->output->writeln('空项目已处理结束');
	}

	public function updateDoc()
	{
		$str = '"uid","wiki_id"
"76052","75"
"259806","76"
"76052","77"
"76052","79"
"259806","80"
"75780","81"
"75780","82"
"75780","83"
"75780","84"
"76052","94"
"348483","144"
"386935","162"
"76052","177"
"76052","268"
"209752","280"
"348483","405"
"328372","410"
"348483","447"
"209752","553"
"348483","701"
"37803","912"
"37803","915"
"328372","932"
"269786","962"
"386935","1026"
"348483","1071"
';
		$str = str_replace('"', '', $str);
		$str = str_replace('"', '', $str);
		$data = explode("\n", $str);

		$data1 = [];
		foreach ($data as $key => $val) {
			if ($val) {
				$new = explode(',', $val);
				if (is_numeric($new[0])) {
					$oldUser = 0;
					$newUser = 0;
					$row = Document::query()->find($new[1]);
					if ($row) {
						$oldUser = $row->creator_id;
					}
					$userThirdParty = UserThirdParty::query()->where('openid', $new[0])->first();
					if ($userThirdParty) {
						$newUser = $userThirdParty->uid;
					}
					$data1[count($data1)] = [
						'openid' => $new[0],
						'document_id' => $new[1],
						'old_user' => $oldUser,
						'new_user' => $newUser
					];
					if ($oldUser == 37 && $newUser) {
						$row->creator_id = $newUser;
						$row->save();
						$this->output->writeln('《'.$row->name.'》:用户归属已调整');
					}
				}
			}
		}
		$this->output->writeln('用户归属已处理结束');
	}
}
