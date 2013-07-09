<?php

class User extends Model {

    public $define = array(
        "id" => "primaryKey",
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