<?php
namespace Router;

class Request {
    
    public $cookies;
    public $query;
    public $body;
    public $params;
    public $server;
    public $validationErrors = array();
    protected $_errors = array();
    
    
    /**
     * Constructor
     */
    public function __construct () {
        $this->query   = (object) $_GET;
        $this->server  = (object) $_SERVER;
        $this->cookies = (object) $_COOKIE;
        
        $this->contentType = $this->server->CONTENT_TYPE;
        $this->requestMethod = $this->server->REQUEST_METHOD;
        
        $this->body = (object)$this->bodyparser();
    }
    
    
    
    
    /**
     * body getter
     *
     * @param String $name
     * @param String|Array $rules
     * @return Mixed Value
     */
    public function body ($name, $filter = null) {
        $val = isset($this->body->{$name}) ? $this->body->{$name} : null;
        if ($filter) $this->_applyRules($filter, $val, $name);
        return $val;
    }
    
    
    
    
    /**
     * Applies rules
     *
     * @param String|Array $rules
     * @param Mixed $val
     * @param String $name
     */
    protected function _applyRules ($rules, $val, $name) {
        if (is_string($rules)) $rules = explode("|", $rules);
        
        $validator = new \Validator($val);
        
        foreach ($rules as $rule) 
        {
            $this->_getValidatorOptions ($rule, $args);
            if (method_exists($validator, $rule))
                $validator = call_user_func_array(array($validator,$rule), $args);
        }
        
        if (!$validator->please()) $this->validationErrors[$name] = $validator->errors();
    }
    
    
    /** 
     * Checks if any validation errors occured
     *
     * @return Bool is valid
     */
    public function isValid () {
        return empty($this->validationErrors);
    }
    
    
    
    
    /**
     * Compiles the rules string to valid operations
     *
     * @param String $fnc
     * @param Array $ops
     */
   protected function _getValidatorOptions (&$fnc, &$ops) {
       $ops = array();
       $needlePos = strpos($fnc, "[");
       if ($needlePos) {
           $ops = substr($fnc, $needlePos + 1, -1);
           $ops = explode(",", $ops);
           $fnc = substr($fnc, 0, $needlePos);
       }
   } 
    
    
    
    /**
     * Bodyparser
     *
     * @return Body Content
     */
    protected function bodyparser () {
        switch ($this->contentType) {
            case "application/x-www-form-urlencoded":
            case "multipart/form-data":
                return (object) $_POST;
                
            case "application/json":
                return json_decode($this->getRawBody());
        }
        
        return null;
    }
    
    
    
    
    /**
     * Gets request body
     * 
     * @return String Request Body
     */
    public function getRawBody () {
        return file_get_contents("php://input");
    }
}
