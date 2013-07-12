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
    
    
    public function getEvents () {
    	$query = new QueryBuilder;
    	$query
    	->select("id", "final_date", "final_location", 
    			 "final_activity", "description", "deadline", "event_token")
    	->from("event_invites")
    	->join("events","event","id")
    	->where("event_invites.user","=",$this->id);
    	return DB::fetch($query,$query->data);
    }
    
    
    public function info () {
        return $this->get("id", "first_name", "last_name", "profile_image");
    }


}