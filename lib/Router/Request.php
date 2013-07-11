<?php
namespace Router;

class Request {


	/**
	 * Cookies
	 */
	public $cookies;


	/**
	 * Query parameters
	 */
	public $query;


	/**
	 * Request body
	 */
	public $body;


	/**
	 * Request parameters
	 *
	 * /path/:someValue -> /path/123 will set $request->params->someValue to 123
	 */
	public $params;


	/**
	 * Stores headers
	 */
	public $headers;


	/**
	 * Server data
	 */
	public $server;


	/**
	 * Stores input validation errors
	 */
	public $validationErrors = array();


	/**
	 * Constructor
	 */
	public function __construct () {
		$this->query   = (object) $_GET;
		$this->cookies = (object) $_COOKIE;
		$this->server  = (object) $_SERVER;
		$this->requestMethod = $this->server->REQUEST_METHOD;
    	
		$this->body    = $this->_bodyparser();
		$this->headers = $this->_getHeaders();
	}


	/**
	 * Getter and validator
	 *
	 * @param String $name
	 * @param String|Array $rules
	 * @return Mixed Value
	 */
	public function __call ($prop, $args) {
		$name   = $args[0];
		$filter = isset($args[1]) ? $args[1] : null;
		$val    = isset($this->$prop->$name) ? $this->$prop->$name : null;
		if ($filter) $this->_applyRules($filter, $val, $name);
		return $val;
	}  


	/**
	 * Applies rules
	 *
	 * @param String|Array $rules
	 * @param Mixed $val
	 * @param String $name
	 */
	protected function _applyRules ($rules, $val, $name) {
		if (is_string($rules)) $rules = explode("|", $rules);
		$validator = new \Validator($val);
    	
		foreach ($rules as $rule) 
		{
			$this->_getValidatorOptions($rule, $args);
			if (method_exists($validator, $rule)) $validator = call_user_func_array(array($validator,$rule), $args);
		}
    	
		if (!$validator->please()) $this->validationErrors[$name] = $validator->errors();
	}


	/** 
	 * Checks if any validation errors occured
	 *
	 * @return Bool is valid
	 */
	public function isValid () {
		return empty($this->validationErrors);
	}


	/**
	 * Compiles the rules string to valid operations
	 *
	 * @param String $fnc
	 * @param Array $ops
	 */
	protected function _getValidatorOptions (&$fnc, &$ops) {
		$ops = array();
		if ($needlePos = strpos($fnc, "[")) {
			$ops = substr($fnc, $needlePos + 1, -1);
			$ops = explode(",", $ops);
			$fnc = substr($fnc, 0, $needlePos);
		}
	} 


	/**
	 * Bodyparser
	 *
	 * @return stdObject Body Content
	 */
	protected function _bodyparser () {
		if (!isset($this->server->CONTENT_TYPE)) return null;
		switch ($this->server->CONTENT_TYPE) {
			case "application/x-www-form-urlencoded":
			case "multipart/form-data":
				return (object) $_POST;
    		
			case "application/json":
				return json_decode($this->getRawBody());
		}
    	
		return null;
	}


	/**
	 * Gets headers
	 *
	 * @return stdObject Headers
	 */
	protected function _getHeaders () {

		$server  = $_SERVER;

		$headers = array();
		

		foreach ($server as $key => $value) {

			if (substr($key, 0, 5) === "HTTP_")
			{
				$headers[str_replace("_","-",substr($key, 5))] = $value;
			}
			elseif (substr($key, 0, 7) === "X_HTTP_")
			{
				$headers["X-" . str_replace("_","-",substr($key, 7))] = $value;
			}

		}
		


		return (object) $headers;
	}



	/**
	 * Gets request body
	 * 
	 * @return String Request Body
	 */
	public function getRawBody () {
		return file_get_contents("php://input");
	}
}
