<?php

$app->group('/api', function() use ($app,$container) {
	$app->post('/register', 'App\Controllers\Api\UserController:register');
	$app->get('/active', 'App\Controllers\Api\UserController:activeUser')->setName('user.active');
    $app->post('/login', 'App\Controllers\Api\UserController:login');

    $app->group('/admin', function() use ($app,$container) {
       $app->post('/create_courses', 'App\Controllers\Api\CoursesController:addCourses');
       $app->get('/courses/{slug}/add_content', 'App\Controllers\Api\CoursesController:getCourse')->setName('api.get.update.courses');
        $app->post('/courses/{slug}/add_content', 'App\Controllers\Api\CoursesController:addCourseContent')->setName('api.put.update.courses');
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