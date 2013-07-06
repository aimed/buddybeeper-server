<?php

class RefreshToken extends Entity {
    
    public $table = "client_refresh_tokens";
    
    public $key = "refresh_token";
    
    public $attributes = array(
        "user",
        "client",
        "scope"
    );
    
}