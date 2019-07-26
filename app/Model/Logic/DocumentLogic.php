<?php


namespace W7\App\Model\Logic;


use W7\App\Model\Entity\Document;

class DocumentLogic extends BaseLogic
{
	public function getdetails($id)
	{
		return Document::find($id);
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
}