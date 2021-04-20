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

use Illuminate\Database\Eloquent\Builder;
use W7\App\Exception\ErrorHttpException;
use W7\Core\Cache\Cache;
use W7\Core\Database\LogicAbstract;
use W7\Core\Database\ModelAbstract;

class BaseLogic extends LogicAbstract
{
	private $cache = null;
	private $prefix = 'document_logic_';

	public function update($id, $data)
	{
		$model = $this->show($id);
		if (!$model->update($data)) {
			throw new ErrorHttpException('保存失败');
		}
		return $model;
	}

	public function store($data)
	{
		/** @var ModelAbstract $model */
		$model = new $this->model($data);
		if (!$model->save()) {
			throw new ErrorHttpException('创建失败');
		}
		return $model;
	}

	public function show($id)
	{
		$row = $this->model::query()->find($id);
		if ($row) {
			return $row;
		}
		throw new ErrorHttpException('资源不存在');
	}

	/**
	 * 关联的模型
	 * @var array
	 */
	protected $with = [];

	/**
	 * @param $condition
	 * @param $orderBy
	 * @param $groupBy
	 * @param $with
	 * @return Builder
	 */
	public function BuildQueryForPaginateOrGet($condition, $orderBy, $groupBy, $with): Builder
	{
		$query = $this->model::query();
		if ($with) {
			$query->with($this->with);
		}
		if ($condition) {
			$this->handleListsCondition($query, $condition);
		}
		if ($groupBy) {
			$query->groupBy($groupBy);
		}
		if ($orderBy) {
			$query->orderByRaw($this->handleOrderByRaw($orderBy));
		}
		return $query;
	}

	public function lists($condition = [], $page = 1, $limit = 20, $orderBy = '', $groupBy = [], $with = '', $columns = ['*'])
	{
		$page = $page < 1 ? 1 : $page;
		$limit = $limit > 5000 ? 5000 : $limit;
		$query = $this->BuildQueryForPaginateOrGet($condition, $orderBy, $groupBy, $with);
		return $query->paginate($limit, $columns, '', $page);
	}

	protected function handleOrderByRaw($orderBy)
	{
		if (stripos($orderBy, 'id') === false) {
			$orderBy = $orderBy . ',id desc';
		}
		return $orderBy;
	}

	/**
	 * 处理查询条件
	 * @param Builder $query
	 * @param $condition
	 */
	protected function handleListsCondition(Builder $query, array $condition)
	{
		foreach ($condition as $where) {
			switch ($where[1]) {
				case 'in':
					$query->whereIn($where[0], $where[2]);
					break;
				case 'between':
					$query->whereBetween($where[0], $where[2]);
					break;
				default:
					$query->where($where[0], $where[1], $where[2]);
			}
		}
	}

	//获取缓存
	public function get($key, $default = null)
	{
		return $this->getCache()->get($this->generateKey($key), $default);
	}

	public function increment($key, $ttl = 24 * 3600, $step = 1)
	{
		$value = $this->get($key);
		if ($value) {
			$value = intval($value) + intval($step);
			$this->set($key, $value);
		} else {
			$this->set($key, 1, $ttl);
		}
		return true;
	}

	public function decrement($key, $ttl, $step = 1)
	{
		$value = $this->get($key);
		if ($value) {
			$value = intval($value) - intval($step);
			$this->set($key, $value);
			return true;
		}
		return false;
	}

	//设置缓存
	public function set($key, $value, $ttl = 24 * 3600)
	{
		return $this->getCache()->set($this->generateKey($key), $value, $ttl);
	}

	//删除缓存
	public function delete($key)
	{
		return $this->getCache()->delete($this->generateKey($key));
	}

	public function getCache()
	{
		if (!$this->cache) {
			$this->cache = new Cache();
		}
		return $this->cache;
	}

	public function generateKey($key)
	{
		return $this->prefix . $key;
	}
}
