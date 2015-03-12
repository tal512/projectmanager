var authControllers = angular.module('authControllers', []);

/**
 * Very basic login controller
 * @todo Is using rootScope good idea?
 * @todo interact with stateProvider requireLogin
 * @todo save authKey between page loads
 * Inspired by:
 * https://github.com/auth0/angularjs-jwt-authentication-tutorial/tree/master/frontend
 * http://brewhouse.io/blog/2014/12/09/authentication-made-simple-in-single-page-angularjs-applications.html
 */
authControllers.controller('LoginCtrl', ['$rootScope', '$scope', '$http', function ($rootScope, $scope, $http) {
	$scope.submit = function (user) {
		var data = {email: user.email, password: user.password};
		$http({
			url: 'api/user/login/',
			method: 'POST',
			data: $.param(data),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (response) {
			if (response.data.status === 'success') {
				$rootScope.currentUser = {};
				$rootScope.currentUser.email = user.email;
				$rootScope.currentUser.authKey = response.data.authKey;
			}
			else {
				alert("Login failed: " + response.data.message);
			}
		});
	}
}]);
