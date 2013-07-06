<?php

class TokenException extends ApiException {
    public $responseCode = 401;
    protected $code = 1004;
    protected $message = "Not Authorized";
}