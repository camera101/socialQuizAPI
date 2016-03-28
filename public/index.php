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
	$app->response->headers->set('Access-Control-Allow-Origin', "http://localhost");
	$app->response->headers->set('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
	$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
	$app->response->body(json_encode($datas));
});
$app->get('/friends/:userId', function ($userId) use ($app) {
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->headers->set('Access-Control-Allow-Origin', "http://localhost");
	$app->response->headers->set('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
	$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
	$data = array();
	$data[] = array('user_id'  => 1,'name'  => 'Johnny John','score' => 88,'activity' => 50,'friends' => 10);
	$data[] = array('user_id'  => 2,'name'  => 'Johnny Not','score' => 76,'activity' => 70,'friends' => 22);
	$data[] = array('user_id'  => 3,'name'  => 'Another Johnny','score' => 0,'activity' => 0,'friends' => 1);
	$data[] = array('user_id'  => 4,'name'  => 'Joanna Hope','score' => 45,'activity' => 50,'friends' => 12);
	$data[] = array('user_id'  => 5,'name'  => 'Johnny Again','score' => 10,'activity' => 56,'friends' => 9);
	
	$app->response->body(json_encode($data));
});
$app->get('/friend/:userId', function ($userId) use ($app) {
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->headers->set('Access-Control-Allow-Origin', "http://localhost");
	$app->response->headers->set('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
	$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
	$data = array();
	$users = array('Johnny', 'John', 'Joanna', 'Hope', 'Bill', 'Billy', 'Boy', 'Bella');
	$users2 = array('Clinton', 'Kennedy', 'Barack', 'Bush', 'Washington', 'Jefferson', 'Reagan', 'Trump');
	$data[] = array('user_id'  => $userId,'name'  => $users[rand(0,7)].' '.$users2[rand(0,7)],'score' => rand(0,100),'activity' => rand(0,100),'friends' => rand(0,100));
	
	$app->response->body(json_encode($data));
});
$app->get('/question/:questionId', function ($questionId) use ($app) {
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->headers->set('Access-Control-Allow-Origin', "http://localhost");
	$app->response->headers->set('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
	$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
	$data = array();
	$answers = array('No', 'Yes', 'Maybe', 'What?', 'Are you talking to me?', 'Red', 'Boy', 'Blue');
	$answers2 = array('Green', 'yellow', 'sky', 'monkey', 'dog', 'cat', 'horse', 'plant');
	$answers3 = array('tree', 'ball', 'Cordova rullz', 'Slim API is nice', 'no more PHP', 'HTTP', 'give me water', 'water');
	$answers4 = array('ping-pong', 'jogging', 'cycling', 'running', 'sky', 'tennis', 'footbal', 'soccer');
	$data[] = array('question_id'  => $questionId,'title'  => 'Is this question '.$questionId.'?','correct'=> 'var'.rand(1,4),'var1' => $answers[rand(0,7)],'var2' => $answers2[rand(0,7)],'var3' => $answers3[rand(0,7)], 'var4' => $answers4[rand(0,7)]);
	
	$app->response->body(json_encode($data));
});
$app->get('/questions/:userId', function ($userId) use ($app) {
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->headers->set('Access-Control-Allow-Origin', "http://localhost");
	$app->response->headers->set('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
	$app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
	$data = array();
	$answer = array(true, false);
	$data[] = array('question_id'  => 1,'title'  => 'Is this question 1?','correct'=> $answer[rand(0,1)]);
	$data[] = array('question_id'  => 2,'title'  => 'Is this question 2?','correct'=> $answer[rand(0,1)]);
	$data[] = array('question_id'  => 3,'title'  => 'Is this question 3?','correct'=> $answer[rand(0,1)]);
	$data[] = array('question_id'  => 4,'title'  => 'Is this question 4?','correct'=> $answer[rand(0,1)]);
	$data[] = array('question_id'  => 5,'title'  => 'Is this question 5?','correct'=> $answer[rand(0,1)]);
	$data[] = array('question_id'  => 6,'title'  => 'Is this question 6?','correct'=> $answer[rand(0,1)]);
	$data[] = array('question_id'  => 7,'title'  => 'Is this question 7?','correct'=> $answer[rand(0,1)]);
	$data[] = array('question_id'  => 8,'title'  => 'Is this question 8?','correct'=> $answer[rand(0,1)]);
	
	$app->response->body(json_encode($data));
});
// Run app
$app->run();
