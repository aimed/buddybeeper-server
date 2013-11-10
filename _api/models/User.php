<?php

class User extends Model {

	public $define = array(
		"id" => "primaryKey",
		"first_name",
		"last_name",
		"password",
		"profile_image",
		"locale",
		"is_default_image",
		"allow_name_change"
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
		->select(
			"events.id AS event.id",
			"events.user AS host.id",
			"first_name AS host.first_name",
			"last_name AS host.last_name",
			"profile_image AS host.profile_image",
			"final_date AS event.final_date", 
			"final_location AS event.final_location", 
			"final_activity AS event.final_activity", 
			"events.created_at AS event.created_at", 
			"description AS event.description",
			"title AS event.title",
			"deadline AS event.deadline", 
			"event_token AS event.token"
		)
		->from("event_invites")
		->join("events","event","events.id")
		->join("users","events.user","users.id")
		->where("event_invites.user","=",$this->id)
		;
		//echo $query;
		
		$result = DB::fetch($query,$query->data);
		$events = array();
		
		if (!$result) return $result;
		
		foreach ($result as $event) {
			
			$new = $this->tomodel("event", $event);
			$new["host"] = $this->tomodel("host", $event);
			
			$e = new Event($new["id"]);
			$new["dates"]	  = $e->dates;
			$new["activities"] = $e->activities;
			$new["comments"]   = $e->comments;
			$new["invites"]	= $e->invites;
			
			$map = $this->remapTo($new["invites"]);
			$new["comments"] = $this->replaceItems($new["comments"],$map);
			
			$events[] = $new;   			
		}
		
		return $events;
	}
	
	private function remapTo ($arr,$id = "id") {
		$remapped = array();
		foreach ($arr as $val) {
			$remapped[$val[$id]] = $val;
		}
		return $remapped;
	}
	
	private function replaceItems ($arr, $map, $to = "user") {
		foreach ($arr as &$i) {
			$i[$to] = $map[$i[$to]];
		}
		return $arr;
	}
	
	private function tomodel ($prefix, $array) {
	
		$prefixlen = strlen($prefix);
		$return = array();
		
		foreach ($array as $key => $value) {
			
			if (substr($key, 0, $prefixlen+1) == $prefix . ".") {
				$return[substr($key, $prefixlen +1 )] = $value;
			} 
		}
		return $return;
	}
	
	public function info () {
		return $this->get("id", "first_name", "last_name", "profile_image", "is_default_image");
	}


}