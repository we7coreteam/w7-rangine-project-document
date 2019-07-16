<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/29
 * Time: 11:10
 */

namespace W7\Laravel\CacheModel;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use W7\Laravel\CacheModel\Caches\BatchCache;
use W7\Laravel\CacheModel\Caches\Cache;

class EloquentBuilder extends Builder
{
	/**
	 * @var Model
	 */
	private $cacheModel;
	
	/**
	 * @param Model $cacheModel
	 */
	public function setCacheModel($cacheModel)
	{
		$this->cacheModel = $cacheModel;
	}
	
	/**
	 * @return Model
	 */
	public function getCacheModel()
	{
		return $this->cacheModel;
	}
	
	public function needCache()
	{
		if (!empty($this->cacheModel)) {
			return $this->getCacheModel()->needCache();
		}
		return false;
	}
	
	/**
	 * @var BatchCache
	 */
	protected $batchCache = null;
	
	public function __construct(QueryBuilder $query)
	{
		parent::__construct($query);
		
		$this->batchCache = new BatchCache(Cache::singleton());
	}
	
	/**
	 * @param       $cacheKey
	 * @param null  $ttl
	 * @param array $columns
	 * @return \Illuminate\Database\Eloquent\Collection
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function cacheGet($cacheKey, $ttl = null, $columns = ['*'])
	{
		$builder = $this->applyScopes();
		
		$models = $this->batchCache->get($cacheKey);
		if (empty($models)) {
			if (count($models = $builder->getModels($columns)) > 0) {
				$models = $builder->eagerLoadRelations($models);
				
				$this->batchCache->set($cacheKey, $models, $ttl);
			}
		}
		
		return $builder->getModel()->newCollection($models);
	}
}