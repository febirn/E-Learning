<?php

$app->group('/api', function() use ($app,$container) {
	$app->post('/register', 'App\Controllers\Api\UserController:register');
	$app->get('/active', 'App\Controllers\Api\UserController:activeUser')->setName('api.user.active');
	$app->post('/login', 'App\Controllers\Api\UserController:login')->setName('api.user.login');
	$app->get('/profile/edit/{id}', 'App\Controllers\Api\UserController:getEditProfile')->setName('api.get.edit.profile.user');
	$app->post('/profile/edit/{id}', 'App\Controllers\Api\UserController:putEditProfile')->setName('api.put.edit.profile.user');
})->add(new App\Middlewares\Api\AuthToken($container));

$app->group('', function() use ($app,$container) {
    $app->get('/', 'App\Controllers\Web\HomeController:index')->setName('web.home');

	$app->get('/register', 'App\Controllers\Web\UserController:getRegister')->setName('web.user.register');
    $app->post('/register', 'App\Controllers\Web\UserController:postRegister');

	$app->get('/active', 'App\Controllers\Web\UserController:activeUser')->setName('web.user.active');

	$app->get('/login', 'App\Controllers\Web\UserController:getLogin')->setName('web.user.login');
    $app->get('/logout', 'App\Controllers\Web\UserController:logout')->setName('web.user.logout');
    $app->post('/login', 'App\Controllers\Web\UserController:postLogin')->setName('web.post.user.login');
    $app->get('/edit_profile', 'App\Controllers\Web\UserController:getEditProfile')->setName('web.user.edit_profile');
    $app->post('/edit_profile', 'App\Controllers\Web\UserController:postEditProfile');
})->add(new \App\Middlewares\Web\AuthWeb($container));

?>
