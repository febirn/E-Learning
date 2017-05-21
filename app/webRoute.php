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

            $app->get('/trash', 'App\Controllers\Web\CourseController:showTrashByIdUser')->setName('web.get.trash.course');

            $app->get('/create', 'App\Controllers\Web\CourseController:getCreateCourse')->setName('web.get.create.course');
            $app->post('/create', 'App\Controllers\Web\CourseController:postCreateCourse');

            $app->get('/{slug}/add_content', 'App\Controllers\Web\CourseController:getCourse')->setName('web.get.update.course');
            $app->post('/{slug}/add_content', 'App\Controllers\Web\CourseController:postAddCourseContent');

            $app->get('/{slug}/course_content', 'App\Controllers\Web\CourseController:getAllCourseContent')->setName('web.get.course.content');

            $app->get('/{slug}/edit/course', 'App\Controllers\Web\CourseController:getEditCourse')->setName('web.edit.course');
            $app->post('/{slug}/edit/course', 'App\Controllers\Web\CourseController:postEditCourse');

            $app->get('/{slug}/edit/course_content/{id}', 'App\Controllers\Web\CourseController:getCourseContent')->setName('web.get.course.content.id');
            $app->post('/{slug}/edit/course_content/{id}', 'App\Controllers\Web\CourseController:putCourseContent');

            $app->get('/{slug}/soft_delete', 'App\Controllers\Web\CourseController:softDelete')->setName('web.soft.delete.course');

            $app->get('/{slug}/restore', 'App\Controllers\Web\CourseController:restore')->setName('web.restore.course');
            
            $app->post('/{slug}/hard_delete', 'App\Controllers\Web\CourseController:hardDelete')->setName('web.hard.delete.course');

            $app->post('/{slug}/hard_delete/course_content/{id}', 'App\Controllers\Web\CourseController:hardDeleteContent')->setName('web.hard.delete.course.content');
        });

        $app->group('/course', function() use($app, $container) {
            $app->get('/search', 'App\Controllers\Api\CourseController:searchByTitle')->setName('web.course.search');
            $app->get('/category/{category}', 'App\Controllers\Web\CourseController:searchByCategory')->setName('web.course.category');
            $app->get('/{slug}', 'App\Controllers\Web\CourseController:searchBySlug')->setName('web.course.slug');
        });
    });

})->add(new \App\Middlewares\Web\AuthWeb($container));
?>