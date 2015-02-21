<?php

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