// Create a new module, projectControllers, which uses projectServices
var navigationControllers = angular.module('navigationControllers', []);

/**
 * Controller for listing all Projects
 **/
navigationControllers.controller('NavigationLinkCtrl', ['$scope', '$location', function($scope, $location) {
	$scope.isActive = function (viewLocation) { 
		return viewLocation === $location.path();
	};
}]);