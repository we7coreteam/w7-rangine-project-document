<?php
/**
 * Created by PhpStorm.
 * User: gorden
 * Date: 3/27/19
 * Time: 11:24 AM
 */

namespace W7\Laravel\CacheModel\Tests\Models;


use W7\Laravel\CacheModel\Model;

class Text extends Model
{
	public $timestamps = false;
	protected $table = 'core_text';
}