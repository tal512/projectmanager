// Create a new module, projectServices, which uses ngResource
var projectServices = angular.module('projectServices', ['ngResource']);

/**
 * Service that fetches all projects from API
 **/
projectServices.factory('Projects', ['$resource',
	function ($resource) {
		return $resource('api/testdata/projects.json', {}, {
			query: {method: 'GET', isArray: true}
		});
}]);