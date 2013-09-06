<?php
namespace Router;

class Base {
	
	
	
	
	/**
	 * Standardizes a URI
	 *
	 * @param String $str The URL string to be prepared
	 * @return String The prepared URL string
	 */
	public static function standardizeURI ($str) {
	
		// Lets make sure the URI ends with a slash
		if (!preg_match("/\/$/",$str)) {
			$str = $str . "/";
		}
		
		return $str;
		
	}
	
	
	
	
	/**
	 * Prepares the requested URI
	 *
	 * @param String $uri The URL string to be prepared
	 * @return String The prepared URL string
	 */
	public static function prepareRequestURI($uri) {
		// We want to strip any get parameters
		$parts = explode("?",$uri);
		$uri = $parts[0];
		$uri = Base::standardizeURI($uri);
		return $uri;
	}
	
	
	
	
	/**
	 * Create regular expression for the route
	 *
	 * @param String $route The route to be prepared
	 * @param Array $params The array wich will contain the parameter names
	 * @return String The routes regular expression
	 */
	public static function prepareRouteExpr($route, &$paramNames = null) {
	
		// wildcarding
		$route = preg_replace("/\/\*\//", "/(.*)/?", $route);
	  
		// Get the parameters required from the route
		$route = preg_replace_callback(
			"/:([a-zA-Z0-9_\-]+)\//", // parameter regex
			function ($match) use (&$paramNames) {
				// pass the parameter name to our array
				if ($paramNames !== null) $paramNames[] = $match[1];
				return "([^/]*)/"; // note: "/" must not be escaped here!
			},
			$route
		);
        		
		// Escape slash
		$route = preg_replace("/\//","\/",$route);
        
		// Wrap it up
		$route = "/^" . $route . "$/i";
		return $route;
	}
	
	
	
	
	/**
	 * Matches a route
	 *
	 * @param String $requested current url
	 * @param String $route Are we there yet?
	 * @param Array Optional
	 */
	public static function matchRoute ($requested, $route, &$params = null) {

		$routeMatches = false;

		// we need to store the parameter names and the test results
		$paramVals;
		$paramKeys = Array();

		// prepare the route
		// @TODO: if no params array is passed, we don't need to get keys
		$route = self::standardizeURI($route);
		$route = self::prepareRouteExpr($route,$paramKeys);

		// test it
		// remember:
		//   paramKeys are the route uri parameters
		//   route at this point is a regular expression
		//   requested is the url we are matching against
		$routeMatches = preg_match($route, $requested, $paramVals);

		// route is correct and parametervalues are required 
		if ($routeMatches && is_array($params) && $paramKeys) {
			// assign values to the parameter keys
			for ($i = 1; $i < count($paramVals); $i++) {
				if (isset($paramVals[$i-1])) {
					  $params[$paramKeys[$i-1]] = urldecode($paramVals[$i]);
				}
			}	
		}
  		
  		return $routeMatches;  
	}

}