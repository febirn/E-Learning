<?php

$app->group('/api', function() use ($app,$container) {
	$app->post('/register', 'App\Controllers\Api\UserController:register');
	$app->get('/active', 'App\Controllers\Api\UserController:activeUser')->setName('user.active');
	$app->post('/login', 'App\Controllers\Api\UserController:login');
	$app->get('/profile/edit/{id}', 'App\Controllers\Api\UserController:getEditProfile')->setName('api.get.edit.profile.user');
	$app->put('/profile/edit/{id}', 'App\Controllers\Api\UserController:putEditProfile')->setName('api.put.edit.profile.user');
})->add(new App\Middlewares\Api\AuthToken($container));

$app->group('', function() use ($app,$container) {
    $app->get('/', 'App\Controllers\Web\HomeController:index')->setName('web.home');

	$app->get('/register', 'App\Controllers\Web\UserController:getRegister')->setName('web.user.register');
    $app->post('/register', 'App\Controllers\Web\UserController:postRegister');

	$app->get('/active', 'App\Controllers\Web\UserController:activeUser')->setName('web.user.active');

	$app->get('/login', 'App\Controllers\Web\UserController:getLogin')->setName('web.user.login');
    $app->get('/logout', 'App\Controllers\Web\UserController:logout')->setName('web.user.logout');
    $app->post('/login', 'App\Controllers\Web\UserController:postLogin')->setName('web.post.user.login');
});

?>
