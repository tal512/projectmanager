<?php

$config = require '/../../application/configs/application.php';

require_once $config['app']['dir'] . '/controllers/Controller.php';
require_once $config['app']['dir'] . '/models/Database.php';

$db = new Database($config['db']);
$container = array(
	'db' => $db,
);

require_once $config['app']['dir'] . '/RequestHandler.php';
$request = new RequestHandler($container, $config);
