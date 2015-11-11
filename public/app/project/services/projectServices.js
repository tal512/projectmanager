// Create a new module, projectServices, which uses ngResource
var projectServices = angular.module('projectServices', ['ngResource', 'authServices']);

/**
 * Service that fetches all projects from API
 **/
projectServices.factory('Projects', ['$resource', 'Authentication',
	function ($resource, Authentication) {
		userInfo = Authentication.getUserInfo();
		return $resource('api/testdata/projects.json', { }, {
			query: {method: 'POST', isArray: true, params: { authKey: userInfo.authKey }},
			cache: false
		});
}]);
