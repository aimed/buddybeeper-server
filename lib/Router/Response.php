<?php
namespace Router;

class Response {

    private $request;
    private $response = null;
    
    public function __construct($request = null) {
        $this->request = $request;
    }
    
    public function jsonp ($data, $status = null) {
        @$callback = $this->request->query->callback;
        if (!$callback) return $this->json($data);
        
        $json = json_encode($data);
        $json = "\")]}',\n\"" . $callback . "(" . $json . ")";
        $this->header("Content-Type","application/javascript");
        $this->send($json);
    }
    
    public function json ($data, $status = null) {
        $this->header("Content-Type","application/json");
        $this->send(json_encode($data));
    }
    
    public function send ($str, $status = null) {
        $this->response = $str;
        if ($status !== null) http_response_code($status); 
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
        $this->jsonp(array("error" => $e->get()), $e->responseCode);
    }
  
}
