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

	$app->group('/admin', function() use ($app,$container) {
        $app->get('', 'App\Controllers\Web\AdminController:index')->setName('web.admin.dashboard');

    	$app->group('/course', function() use ($app,$container) {
            $app->get('/add_admin_course', 'App\Controllers\Web\AdminController:getAddAdminCourse')->setName('web.get.add.admin.course');
            $app->put('/add_admin_course', 'App\Controllers\Web\AdminController:putAddAdminCourse')->setName('web.post.add.admin.course');

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
            $app->post('/{slug}/edit/course_content/{id}', 'App\Controllers\Web\CourseController:putCourseContent')->setName('web.post.course.content.id');

            $app->get('/{slug}/soft_delete', 'App\Controllers\Web\CourseController:softDelete')->setName('web.soft.delete.course');

            $app->get('/{slug}/restore', 'App\Controllers\Web\CourseController:restore')->setName('web.restore.course');
            
            $app->post('/{slug}/hard_delete', 'App\Controllers\Web\CourseController:hardDelete')->setName('web.hard.delete.course');

            $app->post('/{slug}/hard_delete/course_content/{id}', 'App\Controllers\Web\CourseController:hardDeleteContent')->setName('web.hard.delete.course.content');
        });

		$app->group('/article', function() use($app, $container) {
			$app->get('/all', 'App\Controllers\Web\ArticleController:showAll')->setName('web.get.all.article');

			$app->get('/my_article', 'App\Controllers\Web\ArticleController:getArticleByUserId')->setName('web.get.my.article');

			$app->get('/trash', 'App\Controllers\Web\ArticleController:showTrash')->setName('web.get.trash.article');

			$app->get('/create', 'App\Controllers\Web\ArticleController:getCreate')->setName('web.get.create.article');
			$app->post('/create', 'App\Controllers\Web\ArticleController:postCreate')->setName('web.create.article');

			$app->get('/{slug}/edit', 'App\Controllers\Web\ArticleController:getUpdate')->setName('web.get.update.article');
			$app->post('/{slug}/edit', 'App\Controllers\Web\ArticleController:postUpdate')->setName('web.put.update.article');

			$app->post('/{slug}/soft_delete', 'App\Controllers\Web\ArticleController:softDelete')->setName('web.put.soft.delete.article');

			$app->post('/{slug}/hard_delete', 'App\Controllers\Web\ArticleController:hardDelete')->setName('web.delete.hard.delete.article');

			$app->post('/{slug}/restore', 'App\Controllers\Web\ArticleController:restore')->setName('web.put.restore.article');
		});
	})->add(new \App\Middlewares\Web\AdminMiddleware($container));

	$app->group('/course', function() use($app, $container) {
        $app->get('', 'App\Controllers\Web\CourseController:showForUser')->setName('web.course.show.for.user');
        $app->get('/search', 'App\Controllers\Web\CourseController:searchByTitle')->setName('web.course.search');
        $app->get('/category/{category}', 'App\Controllers\Web\CourseController:searchByCategory')->setName('web.course.category');
        $app->get('/{slug}', 'App\Controllers\Web\CourseController:searchBySlug')->setName('web.course.slug');
        $app->get('/{slug}/video/{id}', 'App\Controllers\Web\CourseController:viewVideo')->setName('web.course.view.video');
    });

	$app->group('/article', function() use($app, $container) {
		$app->get('', 'App\Controllers\Web\ArticleController:showForUser')->setName('web.article.show.for.user');

		$app->get('/search', 'App\Controllers\Web\ArticleController:searchByTitle')->setName('web.article.search');

		$app->get('/category/{category}', 'App\Controllers\Web\ArticleController:searchByCategory')->setName('web.article.category');

		$app->get('/{slug}', 'App\Controllers\Web\ArticleController:detail')->setName('web.article.slug');
	});

	$app->get('/{username}', 'App\Controllers\Web\UserController:otherAccount')->setName('web.user.other.account');
})->add(new \App\Middlewares\Web\AuthWeb($container));
?>
