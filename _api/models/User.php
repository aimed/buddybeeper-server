<?php

class User extends Entity {

    public $table = "users";
    public $attributes = array(
        "first_name",
        "last_name",
        "password",
        "profile_image",
        "locale"
    );
    
    
    public function setPassword ($pw) {
        return Vault::hashPassword($pw);
    }
    
    
    public function getChannels () {
        $channel = new UserCommunicationChannel;
        return $channel->findAllByUser($this->id);
    }
    
    
    public function info () {
        return $this->get("id", "first_name", "last_name", "profile_image");
    }


}