// Create a new module, projectServices, which uses ngResource
var projectStatusServices = angular.module('projectStatusServices', 'authServices', ['ngResource']);

/**
 * Service that fetches all projects from API
 **/
projectStatusServices.factory('ProjectStatuses', function ($resource, Authentication) {
	var statuses;
	
	function get() {
		userInfo = Authentication.userInfo();
		var data = {authKey: userInfo.authKey};
		$http({
			url: 'api/status/get/',
			method: 'POST',
			data: $.param(data),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (response) {
			if (response.data.status === 'success') {
				statuses = response.data;
			}
			else {
				alert("Login failed: " + response.data.message);
			}
		}, function(error) {
      alert("Error during login");
    });

    return statuses;
	}
		
	return {
		get: get
	};
});
