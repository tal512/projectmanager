var projectDirectives = angular.module('projectDirectives', []);

/**
 * This is more of a proof of concept, not really sure we should use
 * directive here
 **/
projectDirectives.directive('projectListItemLarge', function () {
	return {
			template: '<h3>{{project.name}}</h3><p>{{project.description}}</p>'
	};
});