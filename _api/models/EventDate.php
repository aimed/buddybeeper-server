<?php

class EventDate extends Entity {

    public $table = "event_dates";
    public $attributes = array(
        "event",
        "user",
        "start",
        "end"
    );
    
}