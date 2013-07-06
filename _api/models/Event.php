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
    
    public function beforeInsert () {
        $this->tmp_user = $this->user;
        $this->user = $this->user->id;
    }
    
    public function afterInsert () {
        $this->user = $this->tmp_user;
    }
    
    public function afterFind () {
        $this->user = new User($this->_storage["user"]);
    }
}