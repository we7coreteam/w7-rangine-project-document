<?php
/**
 * @author donknap
 * @date 18-7-30 下午3:30
 */

namespace W7\Core\Database;

use Illuminate\Database\Eloquent\Builder;
use W7\Laravel\CacheModel\Model;

/**
 * Class ModelAbstract
 * @package W7\Core\Database
 *
 * @method make(array $attributes = [])
 * @method withGlobalScope($identifier, $scope)
 * @method withoutGlobalScope($scope)
 * @method withoutGlobalScopes(array $scopes = null)
 * @method removedScopes()
 * @method whereKey($id)
 * @method whereKeyNot($id)
 * @method where($column, $operator = null, $value = null, $boolean = 'and')
 * @method orWhere($column, $operator = null, $value = null)
 * @method hydrate(array $items)
 * @method fromQuery($query, $bindings = [])
 * @method find($id, $columns = ['*'])
 * @method findMany($ids, $columns = ['*'])
 * @method findOrFail($id, $columns = ['*'])
 * @method findOrNew($id, $columns = ['*'])
 * @method firstOrNew(array $attributes, array $values = [])
 * @method firstOrCreate(array $attributes, array $values = [])
 * @method updateOrCreate(array $attributes, array $values = [])
 * @method firstOrFail($columns = ['*'])
 * @method firstOr($columns = ['*'], Closure $callback = null)
 * @method value($column)
 * @method get($columns = ['*'])
 * @method getModels($columns = ['*'])
 * @method eagerLoadRelations(array $models)
 * @method getRelation($name)
 * @method cursor()
 * @method chunkById($count, callable $callback, $column = null, $alias = null)
 * @method pluck($column, $key = null)
 * @method paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 * @method simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
 * @method create(array $attributes = [])
 * @method forceCreate(array $attributes)
 * @method update(array $values)
 * @method increment($column, $amount = 1, array $extra = [])
 * @method decrement($column, $amount = 1, array $extra = [])
 * @method delete()
 * @method forceDelete()
 * @method onDelete(Closure $callback)
 * @method scopes(array $scopes)
 * @method applyScopes()
 * @method with($relations)
 * @method without($relations)
 * @method newModelInstance($attributes = [])
 * @method getQuery()
 * @method setQuery($query)
 * @method toBase()
 * @method getEagerLoads()
 * @method setEagerLoads(array $eagerLoad)
 * @method getModel()
 * @method setModel(Model $model)
 * @method qualifyColumn($column)
 * @method getMacro($name)
 */
abstract class ModelAbstract extends \Illuminate\Database\Eloquent\Model {

	protected function insertAndSetId(Builder $query, $attributes) {
		$id = $query->insertGetId($attributes, $keyName = $this->getKeyName());

		$this->setAttribute($keyName, $id);
	}

	public function createOrUpdate($condition) {
		return static::query()->updateOrCreate($condition, $this->getAttributes());
	}

	/**
	 * <没有重写功能，只是增加一下注释>
	 * 处理三张表关联的情况，使用此方法
	 *
	 * @param string $related 最终要关联的的表
	 * @param string $through 关联最终表时，需要关联的中间表
	 * @param null $firstKey 中间表关联主表的字段
	 * @param null $secondKey 最终表对应中间表的字段
	 * @param null $localKey 主表中对应中间表的字段
	 * @param null $secondLocalKey 中间表对应最终表的字段
	 * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
	 */
	public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null) {
		return parent::hasManyThrough($related, $through, $firstKey, $secondKey, $localKey, $secondLocalKey);
	}

	static public function instance(){
		return iloader()->singleton(static::class);
	}

	/**
	 * 增加当前表的字段表前缀
	 * @param array $columns
	 */
	static public function qualifyColumns($columns = []) {
		if (empty($columns)) {
			return [];
		}
		$model = static::instance();
		$result = [];
		foreach ($columns as $field) {
			$result[] = $model->qualifyColumn($field);
		}
		return $result;
	}
}
