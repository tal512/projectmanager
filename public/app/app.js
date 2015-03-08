// For jshint (@todo, start using it)
'use strict';

// Init the app, second argument is required modules
var projectManagerApp = angular.module('projectManagerApp', ['ngRoute', 'projectControllers', 'projectServices', 'projectDirectives', 'navigationControllers']);

// Routes
projectManagerApp.config(['$routeProvider',
	function($routeProvider) {
	$routeProvider.
		when('/projects', {
		templateUrl: 'app/project/views/projectListLarge.html',
		controller: 'ProjectListCtrl'
		}).
	otherwise({
		templateUrl: 'app/home/views/welcome.html'
	});
}]);