<?php
/**
 * Created by PhpStorm.
 * User: mayifan
 * Date: 5/19/21
 * Time: 6:03 PM
 */

namespace W7\App\Model\Entity;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserFollower extends Pivot
{
    protected $table = 'user_follower';
    public $dateFormat = 'U';
}
