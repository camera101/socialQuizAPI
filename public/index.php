<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));

// Create monolog logger and store logger in container as singleton 
// (Singleton resources retrieve the same log resource definition each time)
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('slim-skeleton');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Monolog\Logger::DEBUG));
    return $log;
});

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

// Define routes
$app->get('/', function () use ($app) {
    // Sample log message
    $app->log->info("Slim-Skeleton '/' route");
    // Render index view
    $app->render('index.html');
});

$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});

$app->get('/who/:name', function ($name) use ($app) {
	$router = $app->router();
	print_r($router->getCurrentRoute()->getParams());
	
});

$app->get('/get/:id', function ($id) use ($app) {
	//DB connection with medoo 
	// TODO move to init file with config
	$database = new medoo([
		// required
		'database_type' => 'mysql',
		'database_name' => 'api',
		'server' => '127.0.0.1',
		'username' => 'root',
		'password' => '',
		'charset' => 'utf8',
	 
		// [optional]
		'port' => 3306,
	 
		// [optional] Table prefix
		//'prefix' => 'socialApp_',
	 
		// driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
		'option' => [
			PDO::ATTR_CASE => PDO::CASE_NATURAL
		]
	]);
	$datas = $database->select("users", [
		"username",
		"email"
	], [
		"id" => $id
	]);
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body(json_encode($datas));
});
// Run app
$app->run();
