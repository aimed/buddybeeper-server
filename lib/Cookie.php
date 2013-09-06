<?php

class Cookie {
	
	
	/**
	 * Cookie name
	 */
	public $name;
	
	
	/**
	 * Cookie value
	 */
	public $value;
	
	
	/**
	 * Default expiration time
	 */
	public $expires;
	
	
	/**
	 * Default path
	 */
	public $path = "/";
	
	
	/**
	 * Default domain
	 */
	public $domain = "";
	
	
	/**
	 * Default secure setting
	 */
	public $secure = false;
	
	
	/**
	 * Default http only setting
	 */
	public $http_only = false;
	
	
	/**
	 * Cookie type
	 * 
	 * 0 = normal cookie
	 * 1 = cookie with hash
	 * 2 = encrypted cookie
	 */
	private $_type = 0;
	
	
	/**
	 * Cookie Type Normal
	 */
	const normal = 0;
	
	
	/**
	 * Cookie Type Hashed
	 */
	const hashed = 1;
	
	
	/**
	 * Cookie Type Encrypted
	 */
	const encrypted = 2;

	
	/**
	 * Constructor
	 * 
	 * @param String $name
	 * @param String $value
	 * @param Int Type
	 */
	public function __construct ($name, $value = "", $type = 0) {
		$this->name   = $name;
		$this->value  = $value;
		$this->_type  = $type;
		$this->domain = "." . $_SERVER["HTTP_HOST"];
	}
	
	
	/**
	 * Sets the cookie to expires in given time
	 *
	 * @param Int $time
	 * @param String $unit
	 */
	public function expires_in ($time, $unit = "minutes") {
		if (empty($time)) {
			return $this->expires = null; // empty values will result in a session cookie
		}
		
		switch ($unit) {
			case "months" : $time = $time * 60 * 60 * 24 * 31; break;
			case "days"   : $time = $time * 60 * 60 * 24; break;
			case "hours"  : $time = $time * 60 * 60; break;
			case "minutes": $time = $time * 60; break;
		}
		
		$this->expires = time() + $time;
	}
	
	
	/**
	 * Sets the cookie to expires at a given time
	 *
	 * @param Int $time
	 */
	public function expires_at ($time) {
		$this->expires = $time;
	}
	

	/**
	 * Sets the cookie
	 *
	 * @return Bool setcookie successfull
	 */
	public function set () {
		return setcookie($this->name, $this->value, $this->expires, $this->path, $this->domain, $this->secure, $this->http_only);
	}
	
	
	/**
	 * Deletes a cookie
	 *
	 * @return Booke setcookie successfull
	 */
	public function delete () {
		return setcookie($this->name, "", time() - 3600);
	}
}