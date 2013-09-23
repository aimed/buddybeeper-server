/*---
Landing Page Controller
---*/
bb.controller("landingController", ["$scope", "User", function (scope, user) {
	scope.state = "default";
	
	scope.setState = function (string) {
		scope.state = string;
	}
}]);

/*---
Signup Controller
---*/
bb.controller("signupController", ["$scope", "User", function (scope, user) {
	scope.error 	= "";
	scope.email 	= "";
	scope.password 	= "";
	scope.firstName = "";
	scope.lastName 	= "";
	
	scope.signup = function (form) {
		response = user.signup(form, function (r) {
			if (r.code == 1003) form.error = "Invalid field";
			else if (r.response.status != "ok") {
				form.error = "You have already signed up and need to verify your email address.";
			}
		});
		scope.$emit("loginstatechange",response);
	}
}]);

/*---
Signin Controller
---*/
bb.controller("signinController", ["$scope", "User", function (scope, user) {
	scope.error 	= "";
	scope.email 	= "";
	scope.password 	= "";
	
	scope.signin = function (form) {
		response = user.login(form.email, form.username, function (r) {
			if (r.code == 1003) form.error = "Invalid e-mail or password";
		});
		scope.$emit("loginstatechange",response);
		return false;
	}
}]);