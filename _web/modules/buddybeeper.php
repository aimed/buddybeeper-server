<?php

class buddybeeper extends RESTHelper {
	
	
	/**
	 * Base url
	 */
	protected $_baseURL = "http://api.buddybeeper.dev";
	
	
	/**
	 * API Version
	 */
	protected $_version = 0;
	
	
	/**
	 * Client id
	 */
	public $client_id     = "";
	
	
	/**
	 * Client secret
	 */
	public $client_secret = "";
	
	
	/**
	 * Refresh Token
	 */
	public $refresh_token     = "";
	
	
	/**
	 * Access Token
	 */
	public $access_token      = "";
	
	
	/**
	 * Constructor
	 *
	 * @param String $id Optional
	 * @param String $secret Optional
	 */
	public function __construct ($id = "", $secret = "") {
		$this->client_id     = $id;
		$this->client_secret = $secret;
	}
	
	
	/**
	 * Takes a response and tries to set the refresh token
	 *
	 * @param stdClass $response
	 */
	public function setRefreshToken ($response) {
		if (isset($response->response->refresh_token)) $this->refresh_token = $response->response->refresh_token;
	}
	
	
	/**
	 * Takes a response and tries to set the access token
	 *
	 * @param stdClass $response
	 */
	public function setAccessToken ($response) {
		if (isset($response->response->access_token)) $this->access_token = $response->response->access_token;
	}
	
	
	/**
	 * Override post to prepend base url and version
	 * 
	 * @param String $url
	 */
	public function post ($url) {
		return json_decode(parent::post($this->_baseURL . "/v" . $this->_version . $url));
	}
	
	
	/**
	 * Gets a refresh token
	 *
	 * @param String $username
	 * @param String $password
	 */
	public function getRefreshToken ($username, $password) {
		$this->set("client_id", $this->client_id);
		$this->set("client_secret", $this->client_secret);
		$this->set("username", $username);
		$this->set("password", $password);
		
		$response = $this->post("/auth/token");	
		$this->cleanup();
		$this->setRefreshToken($response);
		$this->setAccessToken($response);
		
		return $response;
	}
	
	
	/**
	 * Gets an new access token
	 */
	public function getAccessToken () {
		$this->set("refresh_token", $this->refresh_token);
		
		$response = $this->post("/auth/refresh");	
		$this->cleanup();
		$this->setAccessToken($response);
		
		return $response;
	}
	
	
	/**
	 * Signs up a user
	 *
	 * @param String $email
	 * @param String $password
	 * @param String $first_name
	 * @param String $last_name Optional
	 */
	public function signup ($email, $password, $first_name, $last_name = "") {
		$this->set("email",      $email);
		$this->set("password",   $password);
		$this->set("first_name", $first_name);
		$this->set("last_name",  $last_name);
		
		$response = $this->post("/users");
		$this->cleanup();
		
		
		if (!isset($response->response->status) || $response->response->status != "ok") return $response;
		
		$response = $this->getRefreshToken($email, $password);

		$this->setRefreshToken($response);
		$this->setAccessToken($response);
	
		$response->response->status = "ok";
		return $response;
	}
	
}