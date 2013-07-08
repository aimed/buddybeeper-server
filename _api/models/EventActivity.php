<?php

class EventActivity extends Entity {
    
    public $table = "event_activities";
    public $attributes = array(
        "event",
        "user",
        "activity"
    );
    
}