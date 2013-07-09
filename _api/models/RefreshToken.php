<?php

class RefreshToken extends Model {
    
    public $table = "client_refresh_tokens";
    public $define = array(
        "refresh_token" => "key",
        "user",
        "client",
        "scope"
    );
    
}