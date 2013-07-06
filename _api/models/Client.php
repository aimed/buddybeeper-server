<?php

class Client extends Entity {
    
    public $table = "clients";
    public $attributes = array(
        "name",
        "description",
        "secret"
    );
    
}