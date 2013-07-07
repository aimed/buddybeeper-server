<?php

class Event extends Entity {
    
    public $table = "events";
    public $attributes = array(
        "user",
        "description",
        "final_date",
        "final_activity",
        "final_location",
        "deadline",
        "created_at"
    );
    
    public function setDescription ($text) {
        return Utils::htmlsafe($text);
    }
    
    public function afterFind () {
        $this->user = new User($this->_storage["user"]);
    }
}