var authControllers = angular.module('authControllers', ['authServices']);

/**
 * Very basic login controller
 * @todo Is using rootScope good idea?
 * @todo interact with stateProvider requireLogin
 * @todo save authKey between page loads
 * Inspired by:
 * https://github.com/auth0/angularjs-jwt-authentication-tutorial/tree/master/frontend
 * http://brewhouse.io/blog/2014/12/09/authentication-made-simple-in-single-page-angularjs-applications.html
 */
authControllers.controller('LoginCtrl', ['$scope', 'Authentication', function ($scope, Authentication) {
	$scope.submit = function (user) {
		Authentication.login(user.email, user.password);
	}
}]);
