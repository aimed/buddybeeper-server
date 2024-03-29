<?php


if (!defined("TEMPLATE_DIR")) throw new Exception("Template dir needs to be defined");
if (!defined("TEMPLATE_ARRAY_NAME")) define("TEMPLATE_ARRAY_NAME", "\$_DATA");

class Template {
	
	
	/**
	 * Stores compiled templates
	 */
	protected static $_collection = array();
    
    
    /**
     * Loads the template file
     * @param String filename
     * @return String filecontents
     */
    public static function load ($template, $filename, $locale = "en_US") {
        return file_get_contents(TEMPLATE_DIR . "/" . $template . "/" . $filename);
    }
    
    
    /**
     * Parses a template var
     * @param String $var to be parsed
     * @param String $base current base string to be appended to
     * @return String php conform array name
     */
    public static function parseVar ($var, $base = TEMPLATE_ARRAY_NAME) {
        $needlePos = ($pos = strpos($var, ".")) ? $pos : strlen($var);
			
        $base .= "['" . substr($var, 0, $needlePos) . "']";
        $var = substr($var, $needlePos + 1, strlen($var));
        
        return $var ? self::parseVar($var, $base) : $base;
    }
    
    
    /**
     * Compiles a template file
     * @param String template location
     * @return Function takes data as argument and returns string
     */
    public static function compile ($templateName, $filename) {
        // @TODO: does not allow us to compile mail.txt and sms.txt
        if (isset(self::$_collection[$templateName])) return self::$_collection[$templateName];
        $template = self::load($templateName, $filename);       
        $template = preg_replace_callback("/\{\{([a-zA-Z0-9_\-\.]*)\}\}/", function ($match) {
						return isset($match[1]) ? '".' . Template::parseVar($match[1]) . '."': "";
                    }, $template);
        
        $args = TEMPLATE_ARRAY_NAME . " = array()";
        $code = "return \"" . $template . "\";";
        $function = create_function($args, $code);
        self::$_collection[$templateName] = $function;
        return $function;
    }
    
}
