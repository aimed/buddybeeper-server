<?php

class EventVote extends Model {

    public $table = "event_votes";
    public $attributes = array(
        "user",
        "activity",
        "type",
        "choice"
    );

}