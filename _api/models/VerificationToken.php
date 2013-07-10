<?php

class VerificationToken extends Model {

    public $define = array(
        "token" => "key",
        "type",
        "data",
        "date",
        "reference"
    );
    
    public function beforeInsert () {
        $this->token = Vault::token();
        $this->data = serialize($this->data);
    }
    
    public function afterInsert () {
        $this->data = unserialize($this->data);
    }
    
    public function afterFind () {
        $this->data = unserialize($this->data);
    }
}