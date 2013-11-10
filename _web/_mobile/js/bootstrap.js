var bb = angular.module("buddybeeper",["ngRoute"]);

/*---
Config and bootstrap
---*/
bb.config(["$locationProvider", "$routeProvider", function (locationProvider, routeProvider) {
	locationProvider.html5Mode(true);

	routeProvider
		.when("/", {templateUrl: "/_mobile/home.html"})
		.when("/event/:token", {redirectTo: function (params, path) { return path + "/home"; }})
		.when("/event/:token/:tab", {templateUrl: "/_mobile/event.html"});
}]);

/*---
Root Controller

Handles Menu and Titlebar
---*/
bb.controller("rootController", ["$rootScope", function (root) {
	root.defaultTitle	= "buddybeeper";
	root.title 		    = root.defaultTitle;
	root.menuOpen 		= false;
	root.hasTitleBar 	= false;

	root.showMenu = function (bool) {
		root.menuOpen = bool === undefined ? !root.menuOpen : bool;
		return true;
	}

	root.showTitleBar = function (bool) {
		root.hasTitleBar = bool;
		return true;
	}
	
	root.setTitle = function (string) {
		root.title = string == false ? root.defaultTitle : string;
		return true;
	}
}]);
