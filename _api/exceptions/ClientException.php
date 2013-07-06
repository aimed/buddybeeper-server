<?php

class ClientException extends ApiException {
    public $responseCode = 401;
    protected $code = 1005;
    protected $message = "Client Invalid";
}