// For jshint (@todo, start using it)
'use strict';

// Init the app, second argument is required modules
var projectManagerApp = angular.module('projectManagerApp', ['ui.router',
	'projectControllers',
	'projectServices',
	'projectDirectives',
	'navigationControllers']);

projectManagerApp.run(['$rootScope', '$state', '$stateParams', function ($rootScope,   $state,   $stateParams) {
	// It's very handy to add references to $state and $stateParams to the $rootScope
	// so that you can access them from any scope within your applications.For example,
	// <li ng-class="{ active: $state.includes('contacts.list') }"> will set the <li>
	// to active whenever 'contacts.list' or one of its decendents is active.
	$rootScope.$state = $state;
	$rootScope.$stateParams = $stateParams;
}]);

/**
 * We use ui.router, as it is more flexible than ngRoute
 * https://github.com/angular-ui/ui-router
 * and post about using it with authetication:
 * http://brewhouse.io/blog/2014/12/09/authentication-made-simple-in-single-page-angularjs-applications.html
 */
projectManagerApp.config(['$stateProvider', '$urlRouterProvider', function ($stateProvider,   $urlRouterProvider) {

	// Use $urlRouterProvider to configure any redirects (when) and invalid urls (otherwise).
	$urlRouterProvider
		// If the url is ever invalid, e.g. '/asdf', then redirect to '/' aka the home state
		.otherwise('/');

	$stateProvider
	.state('projects', {
		url: '/projects',
		templateUrl: 'app/project/views/projectListLarge.html',
		controller: 'ProjectListCtrl',
		data: {
			requireLogin: true
		}
	})
	.state('welcome', {
		url: '/',
		templateUrl: 'app/home/views/welcome.html',
		data: {
			requireLogin: false
		}
	});
}]);