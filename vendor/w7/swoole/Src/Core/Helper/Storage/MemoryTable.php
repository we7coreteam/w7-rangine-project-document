<?php
/**
 * @author donknap
 * @date 18-11-14 下午5:20
 */

namespace W7\Core\Helper\Storage;

use Swoole\Table;

class MemoryTable {
	const FIELD_TYPE_STRING = Table::TYPE_STRING;
	const FIELD_TYPE_INT = Table::TYPE_INT;
	const FIELD_TYPE_FLOAT = Table::TYPE_FLOAT;

	private $table = [];

	/**
	 * 创建时可以直接指定一个列数据组用于初始化表，结构如下：
	 * [
	 *      'fieldname' => [type, length],
	 *      'fieldname1' => [type, length],
	 * ]
	 * @param string $name
	 * @param int $size
	 * @param array $column
	 */
	public function create(string $name, int $size, array $column = []) {
		if (!empty($this->table[$name])) {
			return $this->table[$name];
		}

		$table = new Table($size);
		if (!empty($column)) {
			foreach ($column as $field => $params) {
				if (empty($params)) {
					throw new \RuntimeException($field . ' type is null');
				}
				$table->column($field, $params[0], $params[1]);
			}
		}
		if (empty($table->create())) {
			throw new \RuntimeException('Allocation table failed');
		}

		return $table;
	}

	public function get($name) {
		if (empty($this->table[$name])) {
			throw new \RuntimeException('Memory table not exists');
		}
		return $this->table[$name];
	}

	public function getAllName() {
		return array_keys($this->table);
	}
}