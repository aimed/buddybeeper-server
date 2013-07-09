<?php

class Vault {

	/**
	 * URL safe b64 encoding
	 * as seen on http://php.net/manual/de/function.base64-encode.php
	 *
	 * @param String $str
	 * @return String
	 */
	public static function b64_encode ($str) {
		return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
	}

	
	/**
	 * URL safe b64 decoding
	 * 
	 * @param String $str
	 * @return String
	 */
	public static function b64_decode ($str) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($str) % 4, '=', STR_PAD_RIGHT));
	}
	
	
	/**
	 * Creates a salt of specified length
	 *
	 * @param Integer $len Optional
	 * @return String
	 */
	public static function salt ($len = 16) {
		$salt = mcrypt_create_iv($len, MCRYPT_DEV_URANDOM);
		$salt = base64_encode($salt);
		$salt = substr($salt, 0, ($len - strlen($salt)));
		return $salt;
	}
	
	
	/**
	 * Creates a random token
	 *
	 * @param Integer $len Optional
	 * @param Bool $forece_secure Optional
	 * @param Integer $try Optional
	 * @return String
	 */
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
	
	
	/**
	 * Alias for crypt
	 *
	 * @param String $str
	 * @param String $salt Optional
	 */
	public static function hash ($str, $salt = "") {
		return crypt($str, $salt);
	}
	
	
	/**
	 * Encrypts a password
	 * 
	 * @param String $pw
	 * @param String $salt Optional
	 * @return String encrypted password
	 */
	public static function hashPassword ($pw, $salt = null) {
		if(is_null($salt)) $salt = self::salt(22);
		$pw = crypt($pw, $salt);
		$pw .= $salt;
		return $pw;
	}
	
	
	/**
	 * Verifies that an encrypted password matches
	 *
	 * @param String $pw
	 * @param String $hash encrypted password
	 * @return Bool password correct
	 */
	public static function verifyPassword ($pw, $hash) {
		$salt = substr($hash, strlen($hash) - 22, strlen($hash));
		return $hash == self::hashPassword($pw,$salt);
	}

	
	/**
	 * Creates a less secure short hash
	 * 
	 * @param String $str
	 * @param String $salt Optional
	 */
	public static function shortHash ($str, $salt = VAULT_SECRET) {
		return substr(self::hash($str, $salt), 0, 12);
	}

	
	/**
	 * Protects a string against fiddling
	 * 
	 * @param String $str
	 * @return String
	 */
	public static function fiddleProtect ($str) {
		return $str . self::shortHash($str);
	}

	
	/**
	 * Checks if a string has been tempered with
	 *
	 * @param String $str
	 * @return Mixed String if okay - null otherwise
	 */
	public static function fiddleCheck ($str) {
		// check the hash
		$hash  = substr($str, strlen($str) - 12);
		$plain = substr($str, 0, -12);

		if (self::shortHash($plain) !== $hash) $plain = null;
		return $plain;
	}

	
	/**
	 * Encrypts a string
	 * 
	 * @param String $plain
	 * @param String $password
	 * @param String $alg Optional
	 * @param String $mode Optional
	 */
	public static function encrypt ($plain, $password, $alg = "rijndael-256", $mode = "ofb") {
		// append the plain texts hash
		$plain = self::fiddleProtect($plain);

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


	/**
	 * Decrypts a string
	 *
	 * @param String $encrypted
	 * @param String $password
	 * @param String $alg Optional
	 * @param String $mode Optional
	 */
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
          
		return self::fiddleCheck($decrypted);
	}
}
