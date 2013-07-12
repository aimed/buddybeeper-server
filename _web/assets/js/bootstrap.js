// credits for handling login states to 
// http://www.espeo.pl/2012/02/26/authentication-in-angularjs-application
var bb = angular.module("buddybeeper",[]);
bb.config(["$locationProvider", "$routeProvider", "$httpProvider", 
    function ($locationProvider, $routeProvider, $httpProvider) {
        
        /*---
        Routing
        ---*/
        $locationProvider.html5Mode(true);
        $routeProvider.otherwise({templateUrl: "/static/home.html"});
}]);
