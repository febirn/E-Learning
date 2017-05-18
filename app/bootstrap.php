<?php 

session_start();

require __DIR__. '/../vendor/autoload.php';

use Slim\App;

$app = new App([
	'settings'	=> require __DIR__. '/settings.php'
	]);

require __DIR__. '/containers.php';
require __DIR__. '/middlewares.php';
require __DIR__. '/apiRoute.php';
require __DIR__. '/webRoute.php';