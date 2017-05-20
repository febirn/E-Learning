<?php 

$app->group('', function() use ($app,$container) {
    $app->get('/', 'App\Controllers\Web\HomeController:index');

    $app->get('/active', 'App\Controllers\Web\UserController:activeUser')->setName('web.user.active');

    $app->get('/renew_password', 'App\Controllers\Web\UserController:getReNewPassword')->setName('web.user.renew.password');

    $app->group('/admin', function() use ($container, $app) {
        $app->group('/course', function() use ($container, $app) {
            $app->get('/all', 'App\Controllers\Web\CourseController:showAll')->setName('web.get.all.course');
        });
    });

});

?>