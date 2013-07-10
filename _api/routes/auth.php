<?php

$router->post(v0 . "/auth/token", function (&$req, &$res) {

    if (empty($req->body->username)) throw new ParameterException("Username required");
    if (empty($req->body->password)) throw new ParameterException("Password required");
    if (empty($req->body->client_id)) throw new ParameterException("Client ID required");
    if (empty($req->body->client_secret)) throw new ParameterException("Client secret required");
    
    $client = new Client($req->body->client_id);
    if ($client->secret !== $req->body->client_secret || !$client->id) 
        throw new ClientException("Invalid client credentials");
    
    $channel = new UserCommunicationChannel;
    if (!$channel->findByValue($req->body->username) || !$channel->is_bound) 
        throw new ParameterException("Invalid user credentials");
    
    $user = new User($channel->user);
    if (!Vault::verifyPassword($req->body->password, $user->password))
        throw new ParameterException("Invalid user credentials");
    
    $refresh_token = new RefreshToken;
    if (!$refresh_token->findByUserAndClient($user->id, $client->id)) {
        $refresh_token->refresh_token = Vault::token();
        $refresh_token->user = $user->id;
        $refresh_token->client = $client->id;
        $refresh_token->insert();
    }
        
    $access_token = new AccessToken;
    $access_token->issue($refresh_token);
    
    $response = array (
        "refresh_token" => $refresh_token->refresh_token,
        "access_token" => $access_token->access_token,
        "expires_at" => $access_token->expires_at,
        "user" => $user->info()
    );
    
    $res->success($response);
});




$router->post(v0 . "/auth/refresh", function (&$req, &$res) {

    if(empty($req->body->refresh_token)) throw new ParameterException("Refresh token required");
    
    $refresh_token = new RefreshToken($req->body->refresh_token);
    if (!$refresh_token->user) throw new TokenException("Invalid refresh token");
    
    $access_token = new AccessToken;
    $access_token->issue($refresh_token);
    
    $user = new User($refresh_token->user);
    
    $response = $access_token->get("access_token", "expires_at");
    $response["user"] = $user->info();
    
    $res->success($response);
});