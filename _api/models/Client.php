<?php

class Client extends Model {
    
    public $define = array(
        "id" => "primaryKey",
        "name",
        "description",
        "secret"
    );
    
}