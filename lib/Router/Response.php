<?php
namespace Router;

class Response {

    private $request;
    private $response = null;
    
    public function __construct($request = null) {
        $this->request = $request;
    }
    
    public function jsonp ($data) {
        @$callback = $this->request->query->callback;
        if (!$callback) return $this->json($data);
        
        $json = json_encode($data);
        $json = "\")]}',\n\"" . $callback . "(" . $json . ")";
        $this->header("Content-Type","application/javascript");
        $this->response = $json;
    }
    
    public function json ($data) {
        $this->header("Content-Type","application/json");
        $this->response = json_encode($data);
    }
    
    public function send ($str) {
        $this->response = $str;
    }
    
    public function header ($headerKey, $headerValue, $override = true, $status = null) {
        if (!headers_sent()) {
            header($headerKey . ": " . $headerValue, $override, $status);
        }
    }
    
    public function redirect ($to) {
        $this->header("Location", $to);
    }
    
    public function __destruct () {
        if ($this->response !== null) echo $this->response;
    }
    
    public function success ($data) {
        $this->jsonp(array("response" => $data));
    }
    
    public function error (ApiException $e) {
        $this->jsonp(array("error" => $e->get()));
    }
  
}
