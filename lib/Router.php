<?php

class Router extends Router\Base{


	/**
	 * Contains the requested route
	 */
	private $requestedRoute;


	/**
	 * Contains the scope
	 */
	private $scope = array();


	/**
	 * Was the route already provided?
	 */
	private $applied = false;


	/**
	 * Request object
	 */
	private $request;


	/**
	 * Reponse object
	 */
	private $response;


	/**
	 * Constructor
	 */
	public function __construct () {
		// prepare the request object
		$this->request = new Router\Request();
		$this->response = new Router\Response($this->request);
	}


	/**
	 * Request getter
	 */
	public function getRequest () {
		return $this->request;
	}


	/**
	 * Response getter
	 */
	public function getResponse () {
		return $this->response;
	}


	/**
	 * Sets the requested route
	 * 
	 * @param String $requested
	 */
	public function route($requested) {
		$this->requestedRoute = self::prepareRequestURI($requested);
	}


	/**
	 * Stops the routing
	 */
	public function cancel() {
		$this->applied = true;
	}


	/**
	 * Adds a route
	 * 
	 * @param String $method
	 * @param String $route The route to be added
	 * @param Function $callback The callback function
	 * @param String|Array $provide
	 */	
	public function when ($routes, $callback, $provide = array("request", "response"), $method = "") { 
		if ($this->applied === true) return null;
		if ($method !== "" && $this->request->requestMethod !== strtoupper($method)) return null;
		
		$i 		= 0;
		$routes = (array) $routes;
		$count  = sizeof($routes);
		while ($this->applied == false && $i < $count) {
			$params = array();
			if ($this->applied = self::matchRoute($this->requestedRoute, $routes[$i], $params)) {
				$this->request->params = (object) $params;
				call_user_func_array($callback, $this->inject($provide));
			}
			$i++;
		}
	}

	
	// Bind request methods
	public function get ($route, $callback, $provide = array("request", "response")) {
		$this->when($route, $callback, $provide, "get");
	}
	
	public function post ($route, $callback, $provide = array("request", "response")) {
		$this->when($route, $callback, $provide, "post");
	}

	public function put ($route, $callback, $provide = array("request", "response")) {
		$this->when($route, $callback, $provide, "put");
	}

	public function delete ($route, $callback, $provide = array("request", "response")) {
		$this->when($route, $callback, $provide, "delete");
	}

	public function options ($route, $callback, $provide = array("request", "response")) {
		$this->when($route, $callback, $provide, "options");
	}


	/**
	 * Applies functions to certain routes
	 *
	 * if the route applies, the function will be called with
	 * the scope as an argument. If a value is returned, override
	 * scope.
	 * 
	 * @param String $route Optional The route where function should be applied
	 * @param Function $callback The function to be called
	 */
	public function uses ($route, $callback = null, $provide = array("request", "response")) {

		// switch arguments
		if (!is_string($route)) {
			if ($callback !== null) $provide = $callback;
			$callback = $route;
			$route = null;
		}

		// middleware applies? callback time!
		if ($route == null || self::matchRoute($this->requestedRoute, $route)) {
			call_user_func_array($callback, $this->inject($provide));
		}

	}


	/**
	 * Provides middleware and routes with objects
	 *
	 * @param Array $args Objects to provide
	 * @return Array Objects
	 */
	private function inject ($args) {
		
		// allow string based injection
		if (is_string($args)) $args = explode(" ", $args);

		$provide = array();
		$argLen = sizeof($args);

		// call_user_func_array expects references?
		for ($i = 0; $i < $argLen; $i++) {

			switch ($args[$i]) {
				// provide the scope
				case "scope":
					$provide[] = &$this->scope;
					break;

				// provide the self
				case "self":
					$provide[] = &$this;
					break;

				// provide params
				case "req":
				case "request":
					$provide[] = &$this->request;
					break;

				// provide a response object
				case "res":
				case "response":
					$provide[] = &$this->response;
					break;
			}

		}

		return $provide;
	}

}