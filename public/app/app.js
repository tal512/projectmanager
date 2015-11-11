// For jshint (@todo, start using it)
'use strict';

// Init the app, second argument is required modules
var projectManagerApp = angular.module('projectManagerApp', ['ui.router',
	'projectControllers',
	'projectServices',
	'projectDirectives',
	/*'projectStatusServices',*/
	'navigationControllers',
	'authControllers',
	'authServices']);

projectManagerApp.run(['$rootScope', '$state', '$stateParams', '$location', 'Authentication', 
												function ($rootScope, $state, $stateParams, $location, Authentication) {
	// It's very handy to add references to $state and $stateParams to the $rootScope
	// so that you can access them from any scope within your applications.For example,
	// <li ng-class="{ active: $state.includes('contacts.list') }"> will set the <li>
	// to active whenever 'contacts.list' or one of its decendents is active.
	$rootScope.$state = $state;
	$rootScope.$stateParams = $stateParams;
	
	// Initialize the authentication service
	Authentication.init();

	$rootScope.$on('$stateChangeStart',
	function(event, toState, toParams, fromState, fromParams) {
		var requireLogin = toState.data.requireLogin;
		if (requireLogin && typeof Authentication.getUserInfo() === 'undefined') {
			event.preventDefault();
			$state.go('login');
		}
	});
}]);

/**
 * We use ui.router, as it is more flexible than ngRoute
 * https://github.com/angular-ui/ui-router
 * and post about using it with authetication:
 * http://brewhouse.io/blog/2014/12/09/authentication-made-simple-in-single-page-angularjs-applications.html
 */
projectManagerApp.config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {

	// Use $urlRouterProvider to configure any redirects (when) and invalid urls (otherwise).
	$urlRouterProvider
		// If the url is ever invalid, e.g. '/asdf', then redirect to '/' aka the home state
		.otherwise('/');

	$stateProvider
	.state('projects', {
		url: '/projects',
		templateUrl: 'app/project/views/projects.html',
		controller: 'ProjectsCtrl',
		data: {
			requireLogin: true
		}
	})
	.state('projectsAdd', {
		url: '/projects/add',
		templateUrl: 'app/project/views/projectsAdd.html',
		controller: 'ProjectsAddCtrl',
		data: {
			requireLogin: true
		}
	})
	.state('login', {
		url: '/login',
		templateUrl: 'app/auth/views/login.html',
		controller: 'LoginCtrl',
		data: {
			requireLogin: false
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
