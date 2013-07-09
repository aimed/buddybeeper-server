<?php

class UserCommunicationChannel extends Model {

    public $define = array(
        "id" => "primary"
        "user",
        "type",
        "value",
        "active",
        "is_bound"
    );
    
}