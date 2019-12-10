<?php

namespace W7\App\Model\Entity;

class DocumentPermission extends BaseModel {
	const MANAGER_PERMISSION = 1;
	const OPERATOR_PERMISSION = 2;
	const READER_PERMISSION = 3;

	protected $table = 'document_permission';

	public function isManager() : bool {
		return $this->permission == self::MANAGER_PERMISSION;
	}

	public function isOperator() : bool {
		return $this->permission == self::OPERATOR_PERMISSION;
	}

	public function isReader() : bool {
		return $this->permission == self::READER_PERMISSION;
	}
}
