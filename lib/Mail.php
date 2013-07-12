<?php

if (!defined("MAIL_DEFAULT_FROM_ADDRESS")) { 
	trigger_error("No MAIL_DEFAULT_FROM_ADDRESS set", E_USER_NOTICE);
}

class Mail extends Template {
    
	
	/**
	 * Contains the email subject
	 */
	public $subject = "";
	
	
	/**
	 * Contains default data
	 */
	public $defaultData = array();
	
	
	/**
	 * Contains the header string
	 */
	protected $_headers = array(); 
	 
	
	/**
	 * Stores the compiled rendering function
	 */
	protected $render;
	
	
	/**
	 * Constructor
	 *
	 * @param String $template
	 * @param String $from Optional
	 * @param String $subject Optional
	 * @param Array $defaultData Optional
	 */
	public function __construct ($template, $subject = null, $from = null, $defaultData = array()) {
		$this->render      = self::compile($template, "mail.txt");
		$this->subject     = $subject !== null ? $subject : static::load($template, "subject.txt");
		$this->defaultData = $defaultData;
		$this->from($from);
	}
	
	
	/**
	 * Use overloading to set default data
	 *
	 * @param String $key
	 * @param String $value
	 */
	public function __set ($key, $value) {
		$this->defaultData[$key] = $value;
		return $value;
	}
	
	
	/**
	 * Sets a header
	 *
	 * @param String $name
	 * @param String $val
	 */
	public function header ($name, $val) {
		$this->_headers[$name] = $val;
	}
	
	
	/**
	 * Sets the "From" header
	 *
	 * @param String $from Optional Will default to MAIL_DEFAULT_FROM_ADDRESS
	 */
	public function from ($from = null) {
		$this->header("From", empty($from) == false ? $from : MAIL_DEFAULT_FROM_ADDRESS);
	}
	
	
	/**
	 * Gets the header string
	 *
	 * @return String
	 */
	public function getHeaderString () {
		$headers = array();
		foreach ($this->_headers as $name => $value) {
		    $headers[] = $name . ": " . $value;
		}
		return implode("\r\n", $headers);
	}
	
	
	/**
	 * Sends the email
	 *
	 * @param String $to
	 * @param Array $data Optional
	 */
	public function send ($to, $data = array()) {
		$body = call_user_func($this->render,array_merge($this->defaultData, $data));
		mail($to, $this->subject, $body, $this->getHeaderString());
	}
}