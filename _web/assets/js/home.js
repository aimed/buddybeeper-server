bb.controller("event", ["$scope", function (scope) {
	scope.showPeopleList = false;
	scope.mode = "compact";
	scope.isHost = function (event) { return event.host.id == scope.user.id; }
	scope.hasVotedOn = function (item) { return item.votes.indexOf(scope.user.id) > -1; }
	scope.togglePeopleList = function (bool) { scope.showPeopleList = bool; }
}]);

bb.controller("landingPage", ["$scope","User", function (scope, user) {
	scope.login = function () {
		response = user.login(scope);
		if (response.details) scope.loginForm.$error.message = response.details;
		scope.$emit("loginstatechange",response);
	}
}]);
