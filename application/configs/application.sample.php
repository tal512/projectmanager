<?php

/**
 * This is a SAMPLE application configuration file
 * Please rename this to application.php when you deploy the application
 **/
return array(
	'application' => array(
		'baseDir' => 'projects/projectmanager',
	),
	'db' => array(
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'projectmanager',
	),
	'request' => array(
		'defaultController' => 'SiteController',
		'defaultAction' => 'actionIndex',
		'errorController' => 'SiteController',
		'errorAction' => 'actionError',
	),
);