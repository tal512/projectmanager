// Create a new module, projectControllers, which uses projectServices
var projectControllers = angular.module('projectControllers', ['projectServices']);

/**
 * Controller for listing all Projects
 **/
projectControllers.controller('ProjectListCtrl', ['$scope', 'Projects', function($scope, Projects) {
	$scope.projects = Projects.query();
}]);
