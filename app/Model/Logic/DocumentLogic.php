<?php


namespace W7\App\Model\Logic;


use W7\App\Model\Entity\Document;

class DocumentLogic extends BaseLogic
{
	public function add($data)
	{
		return Document::create($data);
	}
}