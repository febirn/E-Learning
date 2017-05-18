<?php

$app->group('/api', function() use ($app,$container) {
	$app->post('/register', 'App\Controllers\Api\UserController:register');
	$app->get('/active', 'App\Controllers\Api\UserController:activeUser')->setName('user.active');
    $app->post('/login', 'App\Controllers\Api\UserController:login');

    $app->group('/admin', function() use ($app,$container) {
        $app->group('/course', function() use ($app,$container) {
            $app->get('/all', 'App\Controllers\Api\CoursesController:showAll')->setName('api.get.all.course');

            $app->get('/my_course', 'App\Controllers\Api\CoursesController:showByIdUser')->setName('api.get.my.course');

            $app->get('/trash', 'App\Controllers\Api\CoursesController:showTrashByIdUser')->setName('api.get.trash.course');

            $app->post('/create', 'App\Controllers\Api\CoursesController:create')->setName('api.get.create.course');

            $app->get('/{slug}/add_content', 'App\Controllers\Api\CoursesController:getCourse')->setName('api.get.update.courses');
            $app->put('/{slug}/add_content', 'App\Controllers\Api\CoursesController:addCourseContent')->setName('api.put.update.courses');
            
            $app->get('/{slug}/soft_delete', 'App\Controllers\Api\CoursesController:softDelete')->setName('api.get.soft.delete.course');

            $app->get('/{slug}/restore', 'App\Controllers\Api\CoursesController:restore')->setName('api.get.restore.course');
            
            $app->delete('/{slug}/hard_delete', 'App\Controllers\Api\CoursesController:hardDelete')->setName('api.get.hard.delete.course');
        });
    });
});

$app->group('', function() use ($app,$container) {
	$app->get('/register', 'App\Controllers\Web\UserController:getRegister')->setName('web.user.register');
    $app->post('/register', 'App\Controllers\Web\UserController:postRegister');
	$app->get('/active', 'App\Controllers\Web\UserController:activeUser')->setName('web.user.active');
	$app->get('/login', 'App\Controllers\Web\UserController:getLogin')->setName('web.user.login');
    $app->post('/login', 'App\Controllers\Web\UserController:postLogin')->setName('web.post.user.login');
});

?>