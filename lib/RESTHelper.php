<?php

class RESTHelper {
	
	
	/**
	 * Stores fields
	 */
	protected $_fields = array();
	
	
	/**
	 * Stores headers
	 */
	protected $_headers = array();
	
	
	/**
	 * Cleans up the object
	 */
	public function cleanup () {
		$this->_fields  = array();
		$this->_headers = array();
	}
	
	
	/**
	 * Set request field
	 *
	 * @param String $name
	 * @param String $value
	 */
	public function set ($name, $value) {
		$this->_fields[$name] = $value;
	}
	
	
	/**
	 * Sets a header
	 *
	 * @param String $name
	 * @param String $value
	 */
	public function header ($name, $value) {
		$this->_headers[$name] = $value;
	}
	
	
	/**
	 * Gets the header array
	 *
	 * @return Array
	 */
	protected function _getHeaders () {
		$headers = array();
		foreach ($this->_headers as $key => $value) {
			if ($value !== null) $headers[] = $key . ": " . $value;
		}
		return $headers;
	}
	
	
	/**
	 * Builds the request string
	 *
	 * @return String
	 */
	protected function _buildRequestString () {
		return http_build_query($this->_fields);
	}
	
	
	/**
	 * Sends a post request
	 *
	 * @param String $url
	 */
	public function post ($url) {
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_buildRequestString());
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		if (!empty($this->_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_getHeaders());
		
		$response = curl_exec($ch);

		curl_close($ch);
		
		return $response;
	}
	
}