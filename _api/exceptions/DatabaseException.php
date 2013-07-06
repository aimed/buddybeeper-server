<?php

class DatabaseException extends ApiException {
    public $responseCode = 500;
    protected $code = 1002;
    protected $message = "Database Error";
}