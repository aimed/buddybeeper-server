<?php

class VerificationToken extends Entity {

    public $key = "token";
    public $table = "verification_tokens";
    public $attributes = array(
        "type",
        "data",
        "date",
        "reference"
    );
    
    public function beforeInsert () {
        $this->key(Vault::token());
        $this->data = serialize($this->data);
    }
    
    public function afterInsert () {
        $this->data = unserialize($this->data);
    }
    
    public function afterFind () {
        $this->data = unserialize($this->data);
    }
}