bb.controller("eventController",
 ["$scope", "$routeParams", "$location", "Event",
 function (scope, routeParams, location, event) {
	scope.token	= routeParams.token;
	scope.tab	= routeParams.tab;
	scope.activities = [{name: 'Go-Cart'}, {name: 'Skydiving'}];
	scope.dates = [{start: 0}, {start: 0}];
	scope.invites = [{first_name: 'Jana', last_name: 'W'}, {first_name: 'John', last_name: 'C'}];
	
	console.log(scope.user);
	
	scope.goTo = function (tab) {
		scope.tab = tab;
		//location.path("/event/" + scope.token + "/" + tab);
	}
	
	scope.isNew = function (data) {
		return 'editable' in data && data.editable === true;
	}
	
	scope.addDate = function () {
		scope.dates.push({start: 0, editable: true});
	}
	
	scope.addActivity = function () {
		scope.activities.push({name:'', editable: true});
	}
}]);