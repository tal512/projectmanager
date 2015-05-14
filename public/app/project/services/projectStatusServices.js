// Create a new module, projectServices, which uses ngResource
var statusServices = angular.module('projectStatusServices', ['ngResource']);

/**
 * Service that fetches all projects from API
 **/
statusServices.factory('ProjectStatuses', ['$resource', 'ProjectStatus', 'Authentication',
	function ($resource, ProjectStatus, Authentication) {
		userInfo = Authentication.userInfo();
		return $resource('api/status/get', {}, {
			query: {method: 'POST', isArray: true, params: { authKey: userInfo.authKey }},
			cache: false
		});
}]);
