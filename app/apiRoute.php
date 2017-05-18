<?php

$app->group('/api', function() use ($app,$container) {
	$app->post('/register', 'App\Controllers\Api\UserController:register');

	$app->get('/active', 'App\Controllers\Api\UserController:activeUser')->setName('api.user.active');

	$app->post('/login', 'App\Controllers\Api\UserController:login');

    $app->post('/password_reset', 'App\Controllers\Api\UserController:passwordReset')->setName('api.user.password.reset');
    $app->get('/renew_password', 'App\Controllers\Api\UserController:getReNewPassword')->setName('api.user.get.renew.password');
    $app->put('/renew_password', 'App\Controllers\Api\UserController:putReNewPassword')->setName('api.user.put.renew.password');
	
    $app->put('/profile/change_password', 'App\Controllers\Api\UserController:changePassword')->setName('api.user.password.change');

    $app->get('/profile/{id}/edit', 'App\Controllers\Api\UserController:getEditProfile')->setName('api.get.edit.profile.user');
    $app->put('/profile/{id}/edit', 'App\Controllers\Api\UserController:putEditProfile')->setName('api.put.edit.profile.user');

    $app->get('/{username}', 'App\Controllers\Api\UserController:otherAccount')->setName('api.user.other.account');

    $app->group('/admin', function() use ($app,$container) {
        $app->group('/course', function() use ($app,$container) {
            $app->get('/all', 'App\Controllers\Api\CoursesController:showAll')->setName('api.get.all.course');

            $app->get('/my_course', 'App\Controllers\Api\CoursesController:showByIdUser')->setName('api.get.my.course');

            $app->get('/trash', 'App\Controllers\Api\CoursesController:showTrashByIdUser')->setName('api.get.trash.course');

            $app->post('/create', 'App\Controllers\Api\CoursesController:create')->setName('api.get.create.course');

            $app->get('/{slug}/add_content', 'App\Controllers\Api\CoursesController:getCourse')->setName('api.get.update.courses');
            $app->post('/{slug}/add_content', 'App\Controllers\Api\CoursesController:addCourseContent')->setName('api.put.update.courses');
            
            $app->get('/{slug}/soft_delete', 'App\Controllers\Api\CoursesController:softDelete')->setName('api.get.soft.delete.course');

            $app->get('/{slug}/restore', 'App\Controllers\Api\CoursesController:restore')->setName('api.get.restore.course');
            
            $app->delete('/{slug}/hard_delete', 'App\Controllers\Api\CoursesController:hardDelete')->setName('api.get.hard.delete.course');
        });
    });
    
})->add(new \App\Middlewares\Api\AuthToken($container));
