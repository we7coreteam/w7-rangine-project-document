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

namespace W7\App\Model\Entity;

class DocumentPermission extends BaseModel
{
	const MANAGER_PERMISSION = 1;
	const OPERATOR_PERMISSION = 2;
	const READER_PERMISSION = 3;

	private $permissionName = [
		self::MANAGER_PERMISSION => '管理员',
		self::OPERATOR_PERMISSION => '操作员',
		self::READER_PERMISSION => '阅读员',
	];

	protected $table = 'document_permission';

	public function save(array $options = [])
	{
		if (in_array($this->permission, [self::MANAGER_PERMISSION, self::OPERATOR_PERMISSION, self::READER_PERMISSION])) {
			return parent::save($options);
		}

		return false;
	}

	public function document()
	{
		return $this->hasOne(Document::class, 'id', 'document_id');
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

	public function getIsManagerAttribute()
	{
		return $this->permission == self::MANAGER_PERMISSION;
	}

	public function getIsOperatorAttribute()
	{
		return $this->permission == self::MANAGER_PERMISSION || $this->permission == self::OPERATOR_PERMISSION;
	}

	public function getIsReaderAttribute()
	{
		return $this->permission == self::MANAGER_PERMISSION || $this->permission == self::OPERATOR_PERMISSION || $this->permission == self::READER_PERMISSION;
	}

	public function getACLAttribute()
	{
		return [
			'name' => $this->permissionName[$this->permission],
			'role' => $this->permission,
			'has_manage' => $this->isManager,
			'has_edit' => $this->isOperator,
			'has_delete' => $this->isManager,
			'has_read' => $this->isReader,
		];
	}

	public function getACLNameAttribute()
	{
		return $this->permissionName[$this->permission];
	}

	public function getRoleListAttribute()
	{
		return $this->permissionName;
	}
}
