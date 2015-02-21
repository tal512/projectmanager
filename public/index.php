<?php

$config = require getcwd() . '/../application/configs/application.php';

require_once getcwd() . '/../application/models/Database.php';
$db = new Database($config['db']);

$container = array(
	'db' => $db,
);

require_once getcwd() . '/../application/RequestHandler.php';
$request = new RequestHandler($container, $config['request']);
