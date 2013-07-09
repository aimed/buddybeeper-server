<?php

class EventActivity extends Model {
    
    public $define = array(
        "id" => "primaryKey",
        "event",
        "user",
        "activity"
    );
    
}