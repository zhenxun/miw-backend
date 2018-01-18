<?php

use Iw\App;
use Slim\Views\Twig;
use Slim\Flash\Messages as Flash;
use Illuminate\Database\Capsule\Manager as Capsule;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

$app = new App();

$container = $app->getContainer();

$capsule = new Capsule();

$capsule->addConnection([
	'driver'=> getEnv('DRIVER'),
	'host'=> getEnv('HOST'),
	'database'=> getEnv('DB'),
	'username'=> getEnv('DBUSER'),
	'password' => getEnv('PASSWORD'),
	'charset' => 'utf8',
	'collation' => 'utf8_general_ci',
	'prefix' => ''
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();


require __DIR__. '/../app/routes.php';
