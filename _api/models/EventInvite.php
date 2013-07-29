<?php

class EventInvite extends Model {

    public $define = array(
        "event_token" => "key",
        "event",
        "user"
    );
    
    
    public function inviteByChannel ($val) {
        $this->event_token = null;
        		
		if (is_array($val)) $val = (object) $val;
		$first_name = (is_object($val) && isset($val->first_name)) ? $val->first_name : null;
		$last_name  = (is_object($val) && isset($val->last_name))  ? $val->last_name  : null;
		$email      = (is_object($val) && isset($val->email))      ? $val->email      : $val;
		
        $channel = new UserCommunicationChannel;
        $channel->findByValue($email);
        
        // create a new dummy user
        if (!$channel->id) 
        {
            if(!Validator::that($email)->isEmail()->please()) return false;

            $user = new User;
            $user->first_name = $first_name;
            $user->last_name  = $last_name;
            $user->allow_name_change = 1;
            $user->save();
            
            // @TODO: gravatar and shit
            
            $channel->user = $user->id;
            $channel->value = $email;
            $channel->type  = "email";
            $channel->save();
        }
        
        $this->user = new User($channel->user);
        if (!$this->user->id)  throw new InternalException("No user set");
        if (!$this->event->id) throw new InternalException("No event set");
        $this->insert();
        
        
        // prepare notifications
        $notificationData = array(
            "host"  => $this->event->user->info(),
            "link"  => $this->buildLink(),
            "event" => $this->event->get("title","description")
        );
        
        
        // send email
        if ($channel->type == "email")
        {
            $mail = new Mail("invite");
            $mail->send($channel->value, $notificationData);
        }
        
        
        return $this->event_token;
    }
    
    
    public function isValid () {
        return !!$this->find(array("event_token"=>$this->event_token));
    }
    
    
    public function beforeInsert () {
        $this->event_token = Vault::token();
        $this->event = $this->event; // fixes event not beeing added to the modified array
    }
    
    public function afterFind () {
        if (isset($this->_storage["user"])) $this->user  = new User($this->_storage["user"]);
        if (isset($this->_storage["event"])) $this->event = new Event($this->_storage["event"]); // @TODO: why?
    }
    
    
    private function buildLink () {
        return BUDDYBEEPER_WEB_URL . "/event?token=" . $this->event_token;
    }
}