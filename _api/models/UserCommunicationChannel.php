<?php

class UserCommunicationChannel extends Model {

    public $define = array(
        "id" => "primaryKey",
        "user",
        "type",
        "value",
        "active",
        "is_bound"
    );
    
}