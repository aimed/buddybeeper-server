<?php

class Router extends Router\Base{
	
	
	/**
	 * Contains the requested route
	 *
	 * @property requestedRoute
	 * @type {String}
	 */
	private $requestedRoute;
	
	
	/**
	 * Contains the scope
	 *
	 * @property scope
	 * @type {Variant}
	 */
	private $scope;
	
	
    /**
     * Was the route already provided?
     *
     * @property applied
     * @type {Bool}
     * @default false
     */
    private $applied = false;
  
  
    /**
     * Stores the request parameter
     *
     * In case we need to provide them,
     * they have to be a propterty
     *
     * @property request
     * @type {Object}
     */
    private $request;
    
    
    /**
     * Stores the request parameter
     *
     * In case we need to provide them,
     * they have to be a propterty
     *
     * @property request
     * @type {Object}
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
	 * @method route
	 * @param {String} $requested
	 */
	public function route($requested) {
  	
  	  // standardize the route, the store it
  		$this->requestedRoute = self::prepareRequestURI($requested);
  		
	}
	
	
	
	
	/**
	 * Stops the routing
	 *
	 * @method cancel
	 */
	public function cancel() {
	    $this->applied = true;
	}
	
	
	
	
	/**
	 * Adds a route
	 * 
	 * @param {String} $route The route to be added
	 * @param {Function} $callback The callback function
	 */	
	private function when ($method, $route, $callback, $provide = array("request", "response")) { 
  	
  		// if the route was already matched, don't proceed
  		if ($this->applied == true || $this->request->requestMethod !== strtoupper($method)) {
  		  	return null;
  		}		
  		
  		// set up the parameter
  		$params = array();
  		
  		// match the route
  		// in case it matches -> set route as applied
  		$this->applied = self::matchRoute($this->requestedRoute, $route, $params);

  		if ($this->applied) {
  		    
  		    // store the params, in case we need to provide them
  		    $this->request->params = (object) $params;

            // it's callback time!
            call_user_func_array($callback, $this->inject($provide));
  		}
	}
  
  
  
  
    public function get ($route, $callback, $provide = array("request", "response")) {
      $this->when("get", $route, $callback, $provide);
    }
    
    
    
    
    public function post ($route, $callback, $provide = array("request", "response")) {
      $this->when("post", $route, $callback, $provide);
    }
    
    
    
    
    public function put ($route, $callback, $provide = array("request", "response")) {
      $this->when("put", $route, $callback, $provide);
    }
    
    
    
    
    public function delete ($route, $callback, $provide = array("request", "response")) {
      $this->when("delete", $route, $callback, $provide);
    }
  
  
  
  
  
	/**
	 * Applies functions to certain routes
	 *
	 * if the route applies, the function will be called with
	 * the scope as an argument. If a value is returned, override
	 * scope.
	 * 
	 * @param {String} $route Optional The route where function should be applied
	 * @param {Function} $callback The function to be called
	 */
	public function uses ($route, $callback = null, $provide = array("request", "response")) {
	
	    // switch arguments
	    if (!is_string($route)) {
	    
	        if ($callback !== null) {
	            $provide = $callback;
	        }
	        
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
	 * @param {array} $args Objects to provide
	 * @return {array} Objects
	 */
	private function inject ($args) {
  	  // allow string based injection
  	  if (is_string($args)) {
  	      $args = explode(" ", $args);
  	  }
  	  
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
      	      case "self": ;
      	      case "self":
        	        $provide[] = &$this;
        	        break;
      	      
      	      // provide params
      	      case "req":
      	      case "request":
      	          $provide[] = &$this->request;
      	          break;
      	      
      	      // provide a request object
      	      case "signedCookies":
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