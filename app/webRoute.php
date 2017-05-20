<?php 

$app->group('', function() use($app,$container) {
	$app->get('/', 'App\Controllers\Web\HomeController:index')->setName('web.home');

	$app->get('/register', 'App\Controllers\Web\UserController:getRegister')->setName('web.user.register');
	$app->post('/register', 'App\Controllers\Web\UserController:postRegister');

	$app->get('/active', 'App\Controllers\Web\UserController:activeUser')->setName('web.user.active');

	$app->get('/login', 'App\Controllers\Web\UserController:getLogin')->setName('web.user.login');
	$app->post('/login', 'App\Controllers\Web\UserController:postLogin')->setName('web.post.user.login');

	$app->get('/logout', 'App\Controllers\Web\UserController:logout')->setName('web.user.logout');

	$app->get('/password_reset', 'App\Controllers\Web\UserController:getPasswordReset')->setName('web.user.password.reset');
	$app->post('/password_reset', 'App\Controllers\Web\UserController:postPasswordReset');

	$app->get('/renew_password', 'App\Controllers\Web\UserController:getReNewPassword')->setName('web.user.renew.password');
	$app->post('/renew_password', 'App\Controllers\Web\UserController:postReNewPassword');

	$app->get('/profile', 'App\Controllers\Web\UserController:myAccount')->setName('web.user.my.account');

	$app->get('/profile/edit', 'App\Controllers\Web\UserController:getEditProfile')->setName('web.user.edit_profile');
	$app->post('/profile/edit', 'App\Controllers\Web\UserController:postEditProfile');

	$app->get('/profile/change_password', 'App\Controllers\Web\UserController:getChangePassword')->setName('web.user.change.password');
	$app->post('/profile/change_password', 'App\Controllers\Web\UserController:postChangePassword');

	$app->get('/profile/premium', 'App\Controllers\Web\UserController:getPremium')->setName('web.user.premium');
	$app->post('/profile/premium', 'App\Controllers\Web\UserController:postPremium');

	$app->get('/{username}', 'App\Controllers\Web\UserController:otherAccount')->setName('web.user.other.account');

    $app->group('/admin', function() use ($container, $app) {
        $app->group('/course', function() use ($container, $app) {
            $app->get('/all', 'App\Controllers\Web\CourseController:showAll')->setName('web.get.all.course');

            $app->get('/my_course', 'App\Controllers\Web\CourseController:showByIdUser')->setName('web.get.my.course');
        });
    });

})->add(new \App\Middlewares\Web\AuthWeb($container));
?>