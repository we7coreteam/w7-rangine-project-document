<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 10:12
 */

namespace W7\Laravel\CacheModel\Tests;


use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;
use W7\Laravel\CacheModel\Caches\BatchCache;
use W7\Laravel\CacheModel\Caches\Tag;
use W7\Laravel\CacheModel\Tests\Models\Member;


class TestTag extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 */
	public function setUp()
	{
		parent::setUp();
		
		\W7\Laravel\CacheModel\Caches\Cache::setCacheResolver(Cache::store());
	}
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function testTag()
	{
		ll(Tag::getPrefix(['a']));
		ll(Tag::getPrefix(['a', 'b']));
		ll(Tag::getPrefix(['a', 'b', 'c']));
	}
	
	public function testSelfDefine()
	{
		ll(Tag::getCacheKey('a', 'w7:ty'));
		ll(Tag::getCacheKey('b', 'w7:ty'));
	}
	
	public function testCollection()
	{
		$arr = collect([1, 2, 3]);
		$arr->each(function ($value, $index) {
			ll($index, $value);
		});
	}
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function testBatchCache()
	{
		$cache_key = 'members_3';
		// $members = Member::query()->take(3)->get();
		//
		$cache = new BatchCache(\W7\Laravel\CacheModel\Caches\Cache::singleton());
		// $cache->set($cache_key, $members);
		
		dd($cache->get($cache_key));
		
	}
	
	public function testGet()
	{
		// $members = Member::query()->take(3)->get();
		$members = Member::query()->where('uid', '<', 10)->with('memberCount')->cacheGet('aaa', 1);
		
		jd($members->keyBy('uid'));
	}
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function testFlush()
	{
		\W7\Laravel\CacheModel\Caches\Cache::singleton()->getCache()->clear();;
		// Member::batchFlush('aaa');;
	}
}