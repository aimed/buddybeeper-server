<?php

class EventActivity extends Model {
    
    public $table  = "event_activities";
    public $define = array(
        "id" => "primaryKey",
        "event",
        "user",
        "name"
    );
    
}