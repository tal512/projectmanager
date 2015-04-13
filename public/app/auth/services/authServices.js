var authServices = angular.module('authServices', ['ngResource']);

/**
 * http://www.sitepoint.com/implementing-authentication-angular-applications/
 */
authServices.factory('Authentication', function($http, $q, $window) {
	var userInfo;

	/**
	 * Loads info from the localstorage on page refresh
	 */
	function init() {
		if ($window.sessionStorage['userInfo']) {
			userInfo = JSON.parse($window.sessionStorage['userInfo']);
		}
	}

	function login(email, password) {
		var data = {email: email, password: password};
		$http({
			url: 'api/user/login/',
			method: 'POST',
			data: $.param(data),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (response) {
			if (response.data.status === 'success') {
				userInfo = {
					email: email,
					authKey: response.data.authKey
				};
				$window.sessionStorage['userInfo'] = JSON.stringify(userInfo);
			}
			else {
				alert("Login failed: " + response.data.message);
			}
		}, function(error) {
      alert("Error during login");
    });

    return userInfo;
	}

	function getUserInfo() {
		return userInfo;
	}

	return {
		init: init,
		login: login,
		getUserInfo: getUserInfo
	};
});
