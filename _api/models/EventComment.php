<?php

class EventComment extends Entity {

    public $table = "event_comments";
    public $attributes = array(
        "user",
        "event",
        "text",
        "pinned",
        "created_at"
    );
    
    
    public function setText ($text) {
        return Utils::htmlsafe($text);
    }
    
    public function afterFind () {
        $this->user = new User($this->_storage["user"]);
        $this->event = new Event($this->_storage["event"]);
    }

}