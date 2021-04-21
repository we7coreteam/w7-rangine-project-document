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
use Illuminate\Support\Str;
use W7\App;
use W7\App\Exception\ErrorHttpException;
use W7\Core\Cache\Cache;
use W7\Core\Database\LogicAbstract;
use W7\Core\Database\ModelAbstract;
use W7\Http\Message\Server\Request;

class BaseLogic extends LogicAbstract
{
	private $cache = null;
	private $prefix = 'document_logic_';

	/**
	 * 需要查询的条件
	 * @var array
	 */
	protected $query = [

	];

	/**
	 * 参数类型
	 * @var array
	 */
	protected $paramType = [];

	/**
	 * 处理条件
	 * @param Request $request
	 */
	protected function beforeBuildQuery(Request $request)
	{
		$with = $request->query('with');
		if ($with) {
			$this->with = $with;
		}
		/** @var BaseLogic $logic */
		$logic = new $this->model();
		$this->condition = $logic->handleCondition($this->query, $this->paramType);
		$this->groupBy = [];
	}

	/**
	 * 处理指定的参数
	 * @param $param
	 * @param $paramType
	 * @return float|int|string
	 */
	protected function handleParamType($param, $paramType)
	{
		$request = App::getApp()->getContext()->getRequest();
		$value = trim($request->input($param, ''));
		if (!isset($paramType[$param])) {
			return $value;
		}
		switch ($paramType[$param]) {
			case 'int':
				$value = intval($value);
				break;
			case 'float':
				$value = floatval($value);
				break;
		}
		return $value;
	}

	/**
	 * 处理查询条件
	 * @param $query
	 * @param array $paramType
	 * @return array
	 */
	public function handleCondition($query, $paramType = [])
	{
		$condition = [];
		$request = App::getApp()->getContext()->getRequest();
		foreach ($query as $symbol => $symbolValue) {
			foreach ($symbolValue as $query) {
				$queryValue = $paramType ? $this->handleParamType($query, $paramType) : $request->input($query);
				if ($queryValue != '') {
					switch ($symbol) {
						case 'in':
						case 'between':
							if (is_string($queryValue)) {
								$queryValue = explode(',', $queryValue);
							}
							break;
						case 'like':
							$queryValue = Str::startsWith($queryValue, '%') ? $queryValue : "%{$queryValue}";
							$queryValue = Str::endsWith($queryValue, '%') ? $queryValue : "{$queryValue}%";
					}
					$condition[] = [$query, $symbol, $queryValue];
				}
			}
		}
		return $condition;
	}

	public function update($id, $data, $checkData = [])
	{
		$model = $this->show($id, $checkData);
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

	public function show($id, $checkData = [], $with = '')
	{
		if ($with) {
			$model = $this->model::query()->with($with)->find($id);
		} else {
			$model = $this->model::query()->find($id);
		}
		if ($model) {
			// 判断是不是当前用户的资源
			if ($checkData) {
				foreach ($checkData as $key => $val) {
					if ($model->$key != $val) {
						throw new ErrorHttpException('没有权限获取该资源');
					}
				}
			}
			return $model;
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
			$query->with($with);
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

	public function lists($condition = [], $page = 1, $limit = 20, $orderBy = 'id desc', $groupBy = [], $with = '', $columns = ['*'])
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
