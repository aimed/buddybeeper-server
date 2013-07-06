<?php

class InternalException extends ApiException {
    public $responseCode = 500;
    protected $code = 1001;
    protected $message = "Internal Error";
}