<?php

class CookieVault extends Vault {
    
    protected static function hashCookie ($val) {
	    return "s(" . Vault::fiddleProtect($val) . ")";
    }
    
    protected static function parseCookie ($val) {
	    return (preg_match("/s\((.+)\)/", $val, $val)) ? 
	    Vault::fiddleCheck($val[1]) : null;
    }

    public static function getCookies ($cookies = null) {
	    
	    $cookies = ($cookies !== null) ? $cookies : $_COOKIE;
	    $secureCookies = array();
        
	    foreach ($cookies as $name => $value) 
	    {
		    if ($value = CookieVault::parseCookie($value)) 
		    {
			    $secureCookies[$name] = $value;
		    }
	    }

	    return $secureCookies;
    }
    
    public static function setCookie (
	$name, $value = "", $expire = 0, $path = null, 
	$domain = null, $secure = false, $httponly = false ) {
      
		$value = CookieVault::hashCookie($value);       
		setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
           
    }
    
}