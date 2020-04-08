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

namespace W7\App\Model\Service;

use W7\App\Model\Entity\Document;
use W7\App\Model\Entity\Document\Chapter;

class PostManService
{
	protected $documentId;
	protected $version;

	public function __construct($documentId,$version=2)
	{
		$this->documentId = $documentId;
		$this->version = $version;
	}

	public function documentToPostMan()
	{
		if(	$this->version==2){
			$data=$this->buildJsonV2();
		}else{
			$data=$this->buildJsonV1();
		}
		return $data;

//		Chapter::query()->where('document_id', $documentId)->where('parent_id', 0)->get();
	}

	public function buildJsonV1(){
		return [];
	}

	public function buildJsonV2(){

		$data = [
			"variables" => [],
			"info" => [
				"name" => "",
				"_postman_id" => "abc-".time()."-".rand(100,999),
				"description" => "",
				"schema" => "https://schema.getpostman.com/json/collection/v2.0.0/collection.json"
			],
			"item" => [
			]
		];
		$documentData=$this->getDocumentData();
		if($documentData['document']){
			$data['info']['name']=$documentData['document']->name;
			$data['info']['description']=$documentData['document']->description;
		}

		return $data;
	}

	public function getDocumentData(){
		$data=[
			'document'=>[],
			'chapter'=>[]
		];
		$documentId = $this->documentId;
		$document=Document::query()->find($documentId);
		if($document){
			$data['document']=$document;
		}
		return $data;
	}

	public function postManToDocument()
	{

	}
}
