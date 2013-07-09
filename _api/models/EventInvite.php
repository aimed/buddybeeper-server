<?php

class EventInvite extends Model {

    public $define = array(
        "event_token" => "key",
        "event",
        "user"
    );
    
    
    public function inviteByChannel ($val) {
        $this->primaryKey(null);

        $channel = new UserCommunicationChannel;
        $channel->findByValue($val);
        
        // create a new dummy user
        if (!$channel->id) 
        {
            $user = new User;
            $user->save();
            
            $channel->user = $user->id;
            
            if(!Validate::that($val)->isMail()->please()) return false;
            $channel->value = $val;
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
            "event" => $this->event->get("description")
        );
        
        
        // send email
        if ($channel->type == "email")
        {
            $notificationData["email"] = $channel->value;
            Mail::send("invite", $notificationData);
        }
        
        
        return $this->key();
    }
    
    
    public function isValid () {
        return $this->event && $this->user && $this->primaryKey();
    }
    
    
    public function beforeInsert () {
        $this->key(Vault::token());
        $this->event = $this->event; // fixes event not beeing added to the modified array
    }
    
    public function afterFind () {
        $this->user  = new User($this->_storage["user"]);
        $this->event = new Event($this->_storage["event"]); // @TODO: why?
    }
    
    
    private function buildLink () {
        return BUDDYBEEPER_WEB_URL . "/event?token=" . $this->key();
    }
}