<?php

class ParameterException extends ApiException {
    public $responseCode = 400;
    protected $code = 1003;
    protected $message = "Invalid Parameter";
}