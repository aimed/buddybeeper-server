<?php

class ApiException extends Exception {
    
    public function __construct ($message = "") {
        $this->detailed = $message;
    }
    
    public function get () {
        return array("code" => $this->code, "message" => $this->message, "details" => $this->detailed);
    }

}