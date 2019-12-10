<?php

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

	public function isManager()
	{
		return $this->permission == self::MANAGER_PERMISSION;
	}

	public function isOperator()
	{
		return $this->permission == self::MANAGER_PERMISSION || $this->permission == self::OPERATOR_PERMISSION;
	}

	public function isReader()
	{
		return $this->permission == self::MANAGER_PERMISSION || $this->permission == self::OPERATOR_PERMISSION || $this->permission == self::READER_PERMISSION;
	}

	/**
	 * 判断用户是否有读该文档权限
	 * @return bool
	 */
	public function hasRead()
	{
		return $this->permission == self::READER_PERMISSION || $this->permission == self::OPERATOR_PERMISSION || $this->permission == self::MANAGER_PERMISSION;
	}

	/**
	 * 判断用户是否有删除该文档权限
	 * @return bool
	 */
	public function hasDelete()
	{
		return $this->permission == self::MANAGER_PERMISSION;
	}

	/**
	 * 判断用户是否有编辑该文档权限
	 * @return bool
	 */
	public function hasEdit()
	{
		return $this->permission == self::MANAGER_PERMISSION || $this->permission == self::OPERATOR_PERMISSION;
	}

	/**
	 * 判断用户是否有管理该文档权限
	 * @return bool
	 */
	public function hasManage()
	{
		return $this->permission == self::MANAGER_PERMISSION;
	}

	public function getACLAttribute()
	{
		return [
			'name' => $this->permissionName[$this->permission],
			'has_manage' => $this->hasManage(),
			'has_edit' => $this->hasEdit(),
			'has_delete' => $this->hasDelete(),
			'has_read' => $this->hasRead(),
		];
	}

	public function getACLNameAttribute()
	{
		return $this->permissionName[$this->permission];
	}

	public function getPermissionList()
	{
		return $this->permissionName;
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
