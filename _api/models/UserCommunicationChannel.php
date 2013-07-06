<?php

class UserCommunicationChannel extends Entity {

    public $table = "user_communication_channels";
    
    public $attributes = array(
        "user",
        "type",
        "value",
        "active",
        "is_bound"
    );
    
}