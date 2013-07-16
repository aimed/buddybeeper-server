bb.controller("sidebar", ["$scope","$location", "$http", function (scope, location, http) {
	
	scope.newEvent = function () {
		location.path("/");
		scope.user.events.push({
			host:scope.user,
			invites:[scope.user],
			activities: [],
			dates: [],
			isNew : true
		});
	}
	
	scope.logout = function () {
		console.log("logging out");
		http.get("/logout").success(function (r) {scope.$emit("loginstatechange");});
		location.path("/");
	}
	
}]);