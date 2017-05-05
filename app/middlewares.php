<?php 

//for add global Middleware

######API###############




######WEB###############

//When response status 404, auto call not found page
$app->add(new \App\Middlewares\NotFoundMiddleware($container));

//csrf
$app->add(new \App\Middlewares\CsrfMiddleware($container));
$app->add($container->csrf);