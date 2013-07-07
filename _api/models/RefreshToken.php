<?php

class RefreshToken extends Entity {
    
    public $key = "refresh_token";
    public $table = "client_refresh_tokens";
    public $attributes = array(
        "user",
        "client",
        "scope"
    );
    
}