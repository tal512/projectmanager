// Create a new module, projectServices, which uses ngResource
var projectServices = angular.module('projectServices', ['ngResource', 'authServices']);

/**
 * Service that fetches all projects from API
 **/
projectServices.factory('Projects', ['$resource', 'Authentication',
	function ($resource, Authentication) {
		return $resource('api/testdata/projects.json', {}, {
			query: {method: 'GET', isArray: true},
			cache: false
		});
}]);
