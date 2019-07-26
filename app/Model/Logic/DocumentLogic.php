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

use W7\App\Model\Entity\Document;

class DocumentLogic extends BaseLogic
{
	public function getlist()
	{
		$res = Document::orderBy('updated_at', 'desc')->get();
		return $this->handleDocumentRes($res);
	}

	public function getdetails($id)
	{
		$res = Document::find($id);
		if ($res && $res['is_show'] == 1) {
			$res['is_show'] = '显示';
		} else {
			$res['is_show'] = '隐藏';
		}
		return $res;
	}

	public function create($data)
	{
		return Document::create($data);
	}

	public function update($id, $data)
	{
		return Document::where('id', $id)->update($data);
	}

	public function del($id)
	{
		return Document::destroy($id);
	}

	public function search($name)
	{
		return Document::where('name', 'like', '%'.$name.'%')->get();
	}

	public function relation($username, $documentId)
	{
		$this->user = new UserLogic();
		$user = $this->user->getUser(['username'=>trim($username)]);
		$document = $this->getdetails($documentId);
		if (!$user) {
			return '用户不存在';
		}
		if (!$document) {
			return '文档不存在';
		}
		if ($user['id'] != $document['creator_id']) {
			return '只有文档创建者才可以操作';
		}
		return true;
	}

	public function handleDocumentRes($res)
	{
		foreach ($res as $key => &$val) {
			if ($val['is_show'] == 1) {
				$val['is_show'] = '显示';
			} else {
				$val['is_show'] = '隐藏';
			}
		}
		return $res;
	}

	public function test()
	{
		$this->test = new UserAuthorizationLogic();
		return $this->test->getUserAuthorizations(2);
	}

	public function getShowList($keyword)
	{
		if ($keyword) {
			$res = Document::where('name', $keyword['name'])
						->orderBy('updated_at', 'desc')
						->get();
		} else {
			$res = Document::orderBy('updated_at', 'desc')->get();
		}
		return $this->handleDocumentRes($res);
	}
}
