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

namespace W7\App\Model\Entity\Document;

use W7\App\Model\Entity\BaseModel;

class ChapterApi extends BaseModel
{
	protected $table = 'document_chapter_api';
	protected $fillable = ['chapter_id', 'url', 'method', 'status_code', 'description', 'body_param_location'];

	const METHOD_GET = 1;
	const METHOD_POST = 2;
	const METHOD_PUT = 3;
	const METHOD_PATCH = 4;
	const METHOD_DELETE = 5;
	const METHOD_COPY = 6;
	const METHOD_HEAD = 7;
	const METHOD_PTIONS = 8;
	const METHOD_LINK = 9;
	const METHOD_UNLINK = 10;
	const METHOD_PURGE = 11;
	const METHOD_LOCK = 12;
	const METHOD_UNLOCK = 13;
	const METHOD_PROPFIND = 14;
	const METHOD_VIEW = 15;

	public static function getMethodLabel()
	{
		return [
			self::METHOD_GET => 'GET',
			self::METHOD_POST => 'POST',
			self::METHOD_PUT => 'PUT',
			self::METHOD_PATCH => 'PATCH',
			self::METHOD_DELETE => 'DELETE',
			self::METHOD_COPY => 'COPY',
			self::METHOD_HEAD => 'HEAD',
			self::METHOD_PTIONS => 'PTIONS',
			self::METHOD_LINK => 'LINK',
			self::METHOD_UNLINK => 'UNLINK',
			self::METHOD_PURGE => 'PURGE',
			self::METHOD_LOCK => 'LOCK',
			self::METHOD_UNLOCK => 'UNLOCK',
			self::METHOD_PROPFIND => 'PROPFIND',
			self::METHOD_VIEW => 'VIEW',
		];
	}

	//状态码
	public static function getStatusCode()
	{
		return [
			200, 301, 403, 404, 500, 502, 503, 504
		];
	}
}
