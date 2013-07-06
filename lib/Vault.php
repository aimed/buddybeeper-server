<?php

class Vault {
    
    // as seen on http://php.net/manual/de/function.base64-encode.php
    public static function b64_encode ($str) {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
    
    public static function b64_decode ($str) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($str) % 4, '=', STR_PAD_RIGHT));
    }
    
	public static function salt ($len = 16) {
	    $salt = mcrypt_create_iv($len, MCRYPT_DEV_URANDOM);
	    $salt = base64_encode($salt);
	    $salt = substr($salt, 0, ($len - strlen($salt)));
	    return $salt;
	}
	
	public static function token ($len = 64, $force_secure = false, $try = 0) {
	
	    $token = self::b64_encode(openssl_random_pseudo_bytes($len/2*1.5, $strong));
	    
	    // try to create a secure token 3 times
	    if ($force_secure === true && $strong === false) 
	    {
	        if ($try > 3) 
	        {
	            throw new Exception("Could not generate secure token");
	        }
	        else
	        {
	            return self::token($len, $try++);
	        }
	    }
	    
	    return $token;    
	}
	
	public static function hash ($str, $salt = "") {
	    return crypt($str, $salt);
	}
	
	public static function hashPassword ($pw, $salt = null) {
	    if(is_null($salt)) $salt = self::salt(22);
	    $pw = crypt($pw, $salt);
	    $pw .= $salt;
	    return $pw;
	}
	
	public static function verifyPassword ($pw, $hash) {
	    $salt = substr($hash, strlen($hash) - 22, strlen($hash));
	    return $hash == self::hashPassword($pw,$salt);
	}
	
	public static function shortHash ($str, $salt = VAULT_SECRET) {
	    return substr(Vault::hash($str, $salt), 0, 12);
	}
	
	public static function fiddleProtect ($str) {
	    return $str . Vault::shortHash($str);
	}
	
	public static function fiddleCheck ($str) {
      // check the hash
      $hash  = substr($str, strlen($str) - 12);
      $plain = substr($str, 0, -12);
      
	  if (Vault::shortHash($plain) !== $hash) 
      {
          $plain = null;
      }
      return $plain;
	}
	
	public static function encrypt ($plain, $password, $alg = "rijndael-256", $mode = "ofb") {
	    // append the plain texts hash
	    $plain = Vault::fiddleProtect($plain);
	    
	    // prepare cipher, iv, password
	    $cipher = mcrypt_module_open($alg, "", $mode, "");
	    $vector = mcrypt_create_iv(mcrypt_get_iv_size($alg, $mode), MCRYPT_DEV_URANDOM);
	    $password = substr(md5($password), 0 , mcrypt_get_key_size($alg, $mode));

	    // initialize
	    mcrypt_generic_init($cipher, $password, $vector);
	    
	    // encrypt
	    $encrypted = mcrypt_generic($cipher, $plain);
	    
	    // terminate
	    mcrypt_generic_deinit($cipher);
	    
	    // append iv to encrypted string
	    $encrypted = base64_encode($vector) . "." . base64_encode($encrypted);
	    
	    return $encrypted;
	}
	
	public static function decrypt ($encrypted, $password, $alg = "rijndael-256", $mode = "ofb") {
        // prepare cipher and password
        $cipher = mcrypt_module_open($alg, "", $mode, "");
        $password = substr(md5($password), 0 , mcrypt_get_key_size($alg, $mode));
        
        // get our encrypted text and the iv
        list($vector, $encrypted) = explode(".", $encrypted, 2);
        $vector = base64_decode($vector);
        $encrypted = base64_decode($encrypted);
        
        // initialize
        mcrypt_generic_init($cipher, $password, $vector);
        
        // decrypt
        $decrypted = mdecrypt_generic($cipher, $encrypted);
        
        // terminate
        mcrypt_generic_deinit($cipher);
                  
        return Vault::fiddleCheck($decrypted);
        
	}
}
