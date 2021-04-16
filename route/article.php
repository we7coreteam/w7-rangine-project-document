<?php
irouter()->middleware(['AppAuthMiddleware', 'CheckAuthMiddleware'])->group(['prefix' => '/article'], function (\W7\Core\Route\Route $route) {

});
