<?php

$app->group('/api', function() use ($app,$container) {
    $app->get('/', 'App\Controllers\Api\HomeController:index')->setName('api.index');
    
	$app->post('/register', 'App\Controllers\Api\UserController:register')->setName('api.user.register');

	$app->get('/active', 'App\Controllers\Api\UserController:activeUser')->setName('api.user.active');

	$app->post('/login', 'App\Controllers\Api\UserController:login')->setName('api.user.login');

    $app->post('/password_reset', 'App\Controllers\Api\UserController:passwordReset')->setName('api.user.password.reset');
    $app->get('/renew_password', 'App\Controllers\Api\UserController:getReNewPassword')->setName('api.user.get.renew.password');
    $app->put('/renew_password', 'App\Controllers\Api\UserController:putReNewPassword')->setName('api.user.put.renew.password');

    $app->get('/braintree/token', 'App\Controllers\Api\BrainTreeController:token')->setName('braintree.token');

    $app->get('/profile/premium', 'App\Controllers\Api\UserController:getBuyPremium')->setName('api.user.premium');
    $app->post('/profile/premium', 'App\Controllers\Api\UserController:postBuyPremium');
	
    $app->put('/profile/change_password', 'App\Controllers\Api\UserController:changePassword')->setName('api.user.password.change');

    $app->get('/profile/{id}/edit', 'App\Controllers\Api\UserController:getEditProfile')->setName('api.get.edit.profile.user');
    $app->post('/profile/{id}/edit', 'App\Controllers\Api\UserController:putEditProfile')->setName('api.put.edit.profile.user');

    $app->get('/{username}', 'App\Controllers\Api\UserController:otherAccount')->setName('api.user.other.account');

    $app->group('/admin', function() use ($app,$container) {
        $app->group('/course', function() use ($app,$container) {
            $app->get('/all', 'App\Controllers\Api\CourseController:showAll')->setName('api.get.all.course');

            $app->get('/my_course', 'App\Controllers\Api\CourseController:showByIdUser')->setName('api.get.my.course');

            $app->get('/trash', 'App\Controllers\Api\CourseController:showTrashByIdUser')->setName('api.get.trash.course');

            $app->post('/create', 'App\Controllers\Api\CourseController:create')->setName('api.get.create.course');

            $app->get('/{slug}/add_content', 'App\Controllers\Api\CourseController:getCourse')->setName('api.get.update.course');
            $app->post('/{slug}/add_content', 'App\Controllers\Api\CourseController:addCourseContent')->setName('api.put.update.course');

            $app->get('/{slug}/edit/course', 'App\Controllers\Api\CourseController:getCourse')->setName('api.get.edit.course');
            $app->post('/{slug}/edit/course', 'App\Controllers\Api\CourseController:editCourse')->setName('api.post.edit.course');

            $app->get('/{slug}/edit/course_content', 'App\Controllers\Api\CourseController:showAllContent')->setName('api.get.course.content');
            $app->post('/{slug}/edit/course_content', 'App\Controllers\Api\CourseController:putCourseContent')->setName('api.put.edit.course.content');
            
            $app->get('/{slug}/soft_delete', 'App\Controllers\Api\CourseController:softDelete')->setName('api.get.soft.delete.course');

            $app->get('/{slug}/restore', 'App\Controllers\Api\CourseController:restore')->setName('api.get.restore.course');
            
            $app->delete('/{slug}/hard_delete', 'App\Controllers\Api\CourseController:hardDelete')->setName('api.get.hard.delete.course');

            $app->delete('/{slug}/hard_delete/course_content/{id}', 'App\Controllers\Api\CourseController:hardDeleteContent')->setName('api.get.hard.delete.course.content');
        });

        $app->group('/course', function() use($app, $container) {
            $app->get('/search', 'App\Controllers\Api\CourseController:searchByTitle')->setName('api.course.search');
            $app->get('/category/{category}', 'App\Controllers\Api\CourseController:searchByCategory')->setName('api.course.category');
            $app->get('/{slug}', 'App\Controllers\Api\CourseController:searchBySlug')->setName('api.course.slug');
        });
    });
    
})->add(new \App\Middlewares\Api\AuthToken($container));
