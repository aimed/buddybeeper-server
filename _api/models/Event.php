<?php

class Event extends Model {
    
    public $define = array(
        "id"   => "primaryKey",
        "user" => "model",
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