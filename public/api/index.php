<?php

$config = require '/../../application/configs/application.php';

$requires = [
	'/BaseClass.php',
	'/controllers/Controller.php',
	'/models/Model.php',
	'/models/Database.php',
];

foreach ($requires as $requiredFile) {
	require_once $config['app']['dir'] . $requiredFile;
}

$db = new Database($config['db']);
$container = array(
	'db' => $db,
);

require_once $config['app']['dir'] . '/RequestHandler.php';
$request = new RequestHandler($container, $config);
