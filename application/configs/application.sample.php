<?php

/**
 * This is a SAMPLE application configuration file
 * Please rename this to application.php when you deploy the application
 */
return [
	'app' => [
		'dir' => substr(__FILE__, 0, -24),
	],
	'db' => [
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'projectmanager',
		'prefix' => '',
	],
	'request' => [
		'defaultController' => 'SiteController',
		'defaultAction' => 'actionIndex',
		'errorController' => 'SiteController',
		'errorAction' => 'actionError',
	],
];
