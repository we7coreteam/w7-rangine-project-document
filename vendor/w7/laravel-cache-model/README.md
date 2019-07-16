# w7Laravel

#### 使用说明

### 1. 注册 `Psr\SimpleCache\CacheInterface` 实现

```
\W7\Laravel\CacheModel\Caches\Cache::setCacheResolver(Cache::store());
```
### 2. 继承 `W7\Laravel\CacheModel\Model` 
```
use W7\Laravel\CacheModel\Model;

class Member extends Model
{
	public $timestamps = false;
	
	protected $table = 'members';
	
	protected $primaryKey = 'uid';

    // 此行可缺省 
	protected $useCache = true;
}
```

### 3. 使用

#### find($id)

```
$uid  = 1;
$user = Member::query()->find($uid);
$user = Member::query()->find($uid);

// query once
// select * from `ims_members` where `ims_members`.`uid` = ? limit 1
```

#### find($ids)
仅限指定 id 查询，不限定返回列。
```
$uids = [1, 2, 5];
Member::query()->find($uids);
Member::query()->find($uids);

// query once
// select * from `ims_members` where `ims_members`.`uid` in (?, ?, ?)
```
#### $model->save();
删除缓存。
```
$member = Member::find($uid)
$member->invite_code = rand(1, 100000);
$member->save();
```
#### $model->update();
删除缓存
#### $model->delete();
删除缓存

#### Member::flush();
清空指定表的缓存

#### Cache::flush();