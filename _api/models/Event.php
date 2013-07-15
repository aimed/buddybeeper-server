<?php

class Event extends Model {
    
    public $define = array(
        "id"   => "primaryKey",
        "user" => "model",
        "title",
        "description",
        "final_date",
        "final_activity",
        "final_location",
        "deadline",
        "created_at"
    );
    
    public function afterFind () {
        $this->user = new User($this->_storage["user"]);
    }
    
    public function getInvites () {
        $query = new QueryBuilder;
        $query
        ->select("users.id", "first_name", "last_name", "profile_image")
        ->from("event_invites")
        ->join("users","user","users.id","left")
        ->where("event","=",$this->id);
        return DB::fetch($query,$query->data);
    }
    
    public function getComments () {
    	$query = new QueryBuilder;
    	$query->select(
    		"id",
    		"user",
    		"text",
    		"pinned"
    	)
    	->from("event_comments")
    	->where("event","=",$this->id);
    	return DB::fetch($query,$query->data);
    }
    
    public function getActivities () {
        $query = new QueryBuilder;
        $query
        ->select("id","name")
        ->append(", GROUP_CONCAT(DISTINCT `event_votes`.`user`) AS `votes`")
        ->from("event_activities")
        ->join("event_votes","id","choice", "left")
        ->and->append("`type`='activity'")
        ->where("event","=",$this->id)
        ->append("GROUP BY `id`");
        $result = DB::fetch($query,$query->data);
        foreach ($result as &$item) {
            $item["votes"] = (!empty($item["votes"])) ? explode(",", $item["votes"]) : array();
        }
        return $result;
    }
    
    public function getDates () {
        $query = new QueryBuilder;
        $query
        ->select("id","start","end")
        ->append(", GROUP_CONCAT(DISTINCT `event_votes`.`user`) AS `votes`")
        ->from("event_dates")
        ->join("event_votes","id","choice", "left")
        ->and->append("`type`='date'")
        ->where("event","=",$this->id)
        ->append("GROUP BY `id`");
        $result = DB::fetch($query,$query->data);
        foreach ($result as &$item) {
            $item["votes"] = (!empty($item["votes"])) ? explode(",", $item["votes"]) : array();
        }
        return $result;
    }
}