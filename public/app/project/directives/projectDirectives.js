var projectDirectives = angular.module('projectDirectives', []);

projectDirectives.directive('projectListItemLarge', function () {
	return {
			template: '{{project.name}} - {{project.description}}'
	};
});