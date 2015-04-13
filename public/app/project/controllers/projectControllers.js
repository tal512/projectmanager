// Create a new module, projectControllers, which uses projectServices
var projectControllers = angular.module('projectControllers', ['projectServices']);

/**
 * Controller for listing all projects
 **/
projectControllers.controller('ProjectsCtrl', ['$scope', 'Projects', function($scope, Projects) {
	$scope.projects = Projects.query();
}]);

/**
 * Controller for adding a new project
 **/
projectControllers.controller('ProjectsAddCtrl', ['$scope', function($scope) {
	$scope.submit = function (project) {
		var data = {status: project.status, name: project.name, description: project.description};
		$http({
			url: 'api/project/create/',
			method: 'POST',
			data: $.param(data),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (response) {
			if (response.data.status === 'success') {
				alert("Project created");
			}
			else {
				alert("Login failed: " + response.data.message);
			}
		}, function(error) {
      alert("Error with creating a new project");
    });
	}
}]);