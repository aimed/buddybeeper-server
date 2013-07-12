<?php

class AccessToken extends Model {
    
    public $table = "client_access_tokens";    
    public $define = array(
        "access_token" => "key",
        "user",
        "client",
        "expires_at",
        "scope"
    );
    
    
    public function issue (RefreshToken $token) {
        if (rand(0, 3) === 3) $this->deleteExpired();
        
        $this->access_token = Vault::token();
        $this->expires_at   = time() + 3600;        
        $this->user         = $token->user;
        $this->client       = $token->client;
        
        $this->insert();
    }
    
    
    public function isValid () {
        return !empty($this->access_token) && $this->expires_at > time() && !!$this->user;
    }
    
    
    public function deleteExpired () {
        $query = new QueryBuilder;
        $query->delete->from($this->table)->where("expires_at","<",time());
        DB::query($query);
    }
}