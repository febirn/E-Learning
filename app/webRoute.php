<?php 

$app->group('', function() use ($app,$container) {
	$app->get('/active', 'App\Controllers\Web\UserController:activeUser')->setName('web.user.active');

	$app->get('/renew_password', 'App\Controllers\Web\UserController:getReNewPassword')->setName('web.user.renew.password');
});

?>