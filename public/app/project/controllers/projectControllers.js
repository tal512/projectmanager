// Create a new module, projectControllers
var projectControllers = angular.module('projectControllers', []);

/**
 * Controller for listing Projects
 * @todo implement fetching the data to projectServices, like in
 *   https://docs.angularjs.org/tutorial/step_11
 **/
projectControllers.controller('ProjectListCtrl', ['$scope', '$http', function($scope, $http) {
	$http.get('api/testdata/projects.json').success(function(data) {
		$scope.projects = data;
	});
}]);