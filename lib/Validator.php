<?php
class Validator {

	/**
	 * Contains the string to be validated
	 */
	private $str;


	/**
	 * Error stack
	 */
	private $err = Array();


	/**
	 * Constructor
	 *
	 * @constructor
	 * @param String $str String that should be tested
	 */
	public function __construct ($str) {
		$this->str = !empty($str) && is_string($str) ? $str : "";
	}

	
	/**
	 * Push error to errorstack
	 *
	 * @method pushErr
	 * @param String $err Errormessage - should default to the function name
	 */
	private function pushErr ($err) {
		$this->err[] = $err;
		return $this;
	}


	/**
	 * checks if empty required
	 */
	public function required ($msg = "Required") {
		if (empty($this->str)) $this->pushErr($msg);
		return $this;
	}


	/**
	 * Is the string between min/max length
	 *
	 * @param Int $min
	 * @param Int $max Optional
	 * @param String $errMsg Optional
	 */
	public function len ($min = 0, $max = false, $errMsg = "Invalid length") {
		if ($this->str == "") return $this;

		if (is_string($max)) 
		{
			$errMsg = $max;
			$max = false;
		}

		$len = strlen($this->str);

		if ($max === false) 
		{
			$max = $len;
		}

		if (($len < $min) || ($len > $max)) 
		{
			$this->pushErr($errMsg);
		}

		return $this;
	}


	/**
	 * Has no special characters
	 *
	 * @param String $errMsg Optional
	 */
	public function hasNoSpecialChar ($errMsg = "Only word characters allowed") {
		if ($this->str == "") return $this;

		if(!ctype_alpha($this->str)) $this->pushErr($errMsg);
		return $this;
	}


	/**
	 * Is it a date of format YYYY-MM-DD
	 *
	 * @TODO: actually check month and date length
	 * 
	 * @param String $errMsg Optional
	 * @param String $str Optional String to match
	 */
	public function isDate ($errMsg = "Invalid date format", $str = null) {
		if ($this->str == "") return $this;

		// we use this as part of isTimestamp
		if ($str === null) $str = $this->str;    

		list($year, $month, $day) = explode("-", $str);

		if (!(isset($year) && isset($month) && isset($day))) 
		{
		    return $this->pushErr($errMsg);
		}

		// check the year part
		if ( (strlen($year) !== 4) || (!is_numeric($year)) ) 
		{
		    return $this->pushErr($errMsg);
		}

		// check the month
		$month = (int) $month;
		if (($month > 12) || ($month < 1)) 
		{
		    return $this->pushErr($errMsg);
		}
        
		// check the month
		$day = (int) $day;
		if (($day < 1) || ($day > 31)) 
		{
		    return $this->pushErr($errMsg);
		}

		return $this;
	}


	/**
	 * Checks if string is a mysql conform timestamp
	 *
	 * @param String $errMsg Optional
	 */
	public function isTimestamp ($errMsg = "Invalid timestamp format") {
		if ($this->str == "") return $this;

		// splite date and time part
		$parts = explode(" ", $this->str);
		if (count($parts) !== 2) 
		{
		    return $this->pushErr($errMsg);
		}

		list($date, $time) = $parts;

		// evaluate date part
		$this->isDate(null, $date);

		// need 3 parts
		$parts = explode(":", $time);
		if (count($parts) !== 3) 
		{
		    return $this->pushErr($errMsg);
		}

		// check if parts are numeric
		foreach ($parts as $part) 
		{
		    if (!is_numeric($part)) 
		    {
		        return $this->pushErr($errMsg);
		    }
		}

		list($hours, $minutes, $seconds) = $parts;

		// hour
		$hours = (int) $hours;
		if (($hours<0) || ($hours > 23)) 
		{
		    return $this->pushErr($errMsg);    
		}

		// minute
		$minutes = (int) $minutes;
		if(($minutes < 0) || ($minutes > 59)) 
		{
		    return $this->pushErr($errMsg);    
		}

		// second
		$seconds = (int) $seconds;
		if (($seconds < 0) || ($seconds > 59)) 
		{
		    return $this->pushErr($errMsg);    
		}

		return $this;   
	}


	/**
	 * Is the string numeric?
	 *
	 * @param String $errMsg Optional
	 */	
	public function isNumeric ($errMsg = "Not a number") {
		if (!is_numeric($this->str)) $this->pushErr($errMsg);
		return $this;
	}


	/**
	 * Boolean (0|1)
	 *
	 * @param String $errMsg Optional
	 */
	public function isBool ($errMsg = "Not boolean") {
		if (!is_bool($this->str)) $this->pushErr($errMsg);
		return $this;
	}


	/**
	 * Match string against given expression
	 *
	 * @param RegExp $expr
	 * @param String $errMsg Optional
	 */	
	public function matches ($expr, $errMsg = "Invalid string") {
		if ($this->str == "") return $this;

		if (!preg_match($expr, $this->str)) $this->pushErr($errMsg);
		return $this;
	}


	/**
	 * Validate email
	 *
	 * Credits to http://www.linuxjournal.com/article/9585
	 *
	 * @param String $errMsg Optional
	 */	
	public function isEmail ($errMsg = "Is no email") {
		if ($this->str == "") return $this;

		$email = $this->str;
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex)
		{
			$isValid = false;
		}
		else
		{
		  $domain = substr($email, $atIndex+1);
		  $local = substr($email, 0, $atIndex);
		  $localLen = strlen($local);
		  $domainLen = strlen($domain);
		  if ($localLen < 1 || $localLen > 64)
		  {
		     // local part length exceeded
		     $isValid = false;
		  }
		  else if ($domainLen < 1 || $domainLen > 255)
		  {
		     // domain part length exceeded
		     $isValid = false;
		  }
		  else if ($local[0] == '.' || $local[$localLen-1] == '.')
		  {
		     // local part starts or ends with '.'
		     $isValid = false;
		  }
		  else if (preg_match('/\\.\\./', $local))
		  {
		     // local part has two consecutive dots
		     $isValid = false;
		  }
		  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		  {
		     // character not valid in domain part
		     $isValid = false;
		  }
		  else if (preg_match('/\\.\\./', $domain))
		  {
		     // domain part has two consecutive dots
		     $isValid = false;
		  }
		  else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local)))
		  {
		     // character not valid in local part unless 
		     // local part is quoted
		     if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local)))
		     {
		        $isValid = false;
		     }
		  }
		  if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
		  {
		     // domain not found in DNS
		     $isValid = false;
		  }
		}

		if (!$isValid) $this->pushErr($errMsg);
	
		return $this;
	}


	/**
	 * Returns the result of the validations
	 *
	 * @return Bool is valid?
	 */
	public function please () {
		return sizeof($this->err) === 0;
	}


	/**
	 * Is String
	 */
	public function string ($msg = "Is no string") {
		if (!is_string($this->str)) $this->pushErr($msg);
		return $this;
	}


	/**
	 * Returns the error stack
	 *
	 * @return Array Error Stack
	 */	
	public function errors () {
		return $this->err;
	}


	/**
	 * Factory
	 */
	public static function that ($str) {
		return new static($str);
	}
}