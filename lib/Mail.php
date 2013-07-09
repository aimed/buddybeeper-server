<?php

if (!defined("MAIL_DEFAULT_FROM_ADDRESS")) { 
	trigger_error("No MAIL_DEFAULT_FROM_ADDRESS set", E_USER_NOTICE);
}

class Mail extends Template {
    
	
	/**
	 * Contains the email subject
	 */
	public $subject;
	
	
	/**
	 * Contains the from header
	 */
	public $from;
	
	
	/**
	 * Contains the subject
	 */
	public $subject;
	
	
	/**
	 * Contains default data
	 */
	public $defaultData;
	
	
	/**
	 * Constructor
	 *
	 * @param String $template
	 * @param String $from Optional
	 * @param String $subject Optional
	 * @param Array $defaultData Optional
	 */
	public function __construct ($template, $from = MAIL_DEFAULT_FROM_ADDRESS, $subject = null, $defaultData = array()) {
		$this->render      = static::compile($template, "mail.txt");
		$this->form        = $from;
		$this->subject     = $subject ? $subject === null : static::load($template, "subject.txt");
		$this->defaultData = $defaultData;
	}
	
	
	/**
	 * Sends the email
	 *
	 * @param String $to
	 * @param Array $data Optional
	 */
	public function send ($to, $data = array()) {
		mail($to, $this->subject, $this->render(array_merge($this->defaultData, $data)), $this->from);
	}
	

	/**
	 * Sends an email
	 * 
	 * @param String $template
	 * @param Array $data
	 */
	public static function send ($template, (array)$data) {

	    $render  = self::compile($template, "mail.txt");
	    $subject = self::load($template, "subject.txt");
    
	    if (isset($data["email"])) mail($data["email"], $subject, $render($data), "From: noreply@buddybeeper.net");
	}
}