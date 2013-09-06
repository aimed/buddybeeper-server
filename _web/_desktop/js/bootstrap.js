// credits for handling login states to 
// http://www.espeo.pl/2012/02/26/authentication-in-angularjs-application
var bb = angular.module("buddybeeper",[]);
bb.config(["$locationProvider", "$routeProvider", "$httpProvider",
    function ($locationProvider, $routeProvider, $httpProvider) {
		
		var staticPrefix = "_desktop";        

        /*---
        Routing
        ---*/
        $locationProvider.html5Mode(true);
        $routeProvider
        .when("/event",{templateUrl: "/" + staticPrefix + "/event.html"})
        .otherwise({templateUrl: "/" + staticPrefix + "/home.html"});
        
}]);

/**
 * Controller: rootController
 *
 * 
 */
bb.controller("rootController",["$rootScope", "User", function (scope, user) {
	scope.hasSideBar = false;
	scope.events     = null;
	scope.toggleSideBar = function (bool) {scope.hasSideBar = !!bool; return true;}
}]);