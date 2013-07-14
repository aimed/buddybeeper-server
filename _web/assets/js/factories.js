/**
 * Factory: User
 *
 *
 */
bb.factory("User", ["$rootScope", "$http", function (scope, http) {
	scope.user = {};
	scope.isLoggedIn = false;
	scope.$on("loginstatechange", function (event,state) { 
		scope.isLoggedIn = !!(state && state.access_token);
		scope.user = (state && state.user) ? state.user : {};
	});
	
	var user = {
		ping : function () {
			scope.$emit("loginstatechange",static.ping.should ? static.ping.success : static.ping.fail);
		},
		
		login : function (form, force) {
		
			var response;
			if (force || form.password === "123") {
				response = static.loginResponse.success;
			} else { 
				response = static.loginResponse.fail; 
			}
			
			scope.$emit("loginstatechange",response);
			return response;
		}
	}
	
	user.ping();
	return user;
}]);