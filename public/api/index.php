<?php

$config = require '../../application/configs/application.php';

/**
 * Provides compability for PHP >= 5.3.7
 * https://github.com/ircmaxell/password_compat
 */
require '../../lib/password_compat/password.php';

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
	'config' => $config,
	'db' => $db,
);

require_once $config['app']['dir'] . '/RequestHandler.php';
$request = new RequestHandler($container, $config);
