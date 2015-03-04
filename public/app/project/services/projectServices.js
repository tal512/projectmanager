/**
 * Currently not in use, due to some mysterious error
 * @todo Make this work, tutorial: 
 *   https://docs.angularjs.org/tutorial/step_11
 **/

var projectServices = angular.module('projectServices', ['ngResource']);

projectServices.factory('Projects', ['$resource',
	function ($resource) {
		return $resource('../../../api/testdata/projects.json', {}, {
			query: {method:'GET', isArray:true}
		});
}]);