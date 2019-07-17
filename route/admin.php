<?php

irouter()->middleware('AdminMiddleware')->group(['prefix'=>'/admin'],function(\W7\Core\Route\Route $route){

});


