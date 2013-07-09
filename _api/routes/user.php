<?php

$router->get(v0 . "/users/:id", function (&$req, &$res) {

    $user = new User($req->params->id);
    $response = $user->info();
    
    if (!$user->id) throw new ParameterException("User not found");
    
    $res->success($response);
});




$router->post(v0 . "/users", function (&$req, &$res) {
    
    $email      = $req->body("email",      "required|email");
    $password   = $req->body("password",   "required|len[6]");    
    $first_name = $req->body("first_name", "required|len[2,50]|hasNoSpechialChar");
    $last_name  = $req->body("last_name",  "len[2,50]|hasNoSpechialChar");
    if (!$req->isValid()) throw new ParameterException($req->validationErrors);
    
    $channel = new UserCommunicationChannel;
    $channelexists = !!$channel->findByValue($email);
    
    if ($channel->is_bound) throw new ParameterException("Email already in use");
    elseif ($channelexists) 
    {
        $data = (object) compact($first_name, $last_name, $password);
        $data->user = $channel->user;
        
        $token = new VerificationToken;
        $token->type      = "signup";
        $token->reference = $channel;
        $token->data      = $data;
        $token->insert();
        
        Mail::send("verification", array(
            "email"      => $email,
            "first_name" => $first_name,
            "link"       => BASE_URL. v0 . "/verify?token=" . $token->token
        ));
        
        
        $response = array("status"=>"confirm");
    }
    else 
    {
        $user = new User;
        $user->first_name = $first_name;
        $user->last_name  = $last_name;
        $user->password   = $password;
        $user->save();

        $channel->type  = "email";
        $channel->value = $email;
        $channel->user  = $user;
        $channel->save();

        $response = $user->info();
        $response["status"] = "ok";
    }
    
    return $res->success($response);
});




$router->get(v0 . "/verify", function (&$req, &$res) {
    $token = new VerificationToken($req->query->token);
        
    $user = new User($token->data->user);
    $user->first_name = $token->data->first_name;
    $user->last_name  = $token->data->last_name;
    $user->password   = $token->data->password;
    $user->save();
    
    $channel = new UserCommunicationChannel($token->reference);
    $channel->is_bound = 1;
    $channel->save();
    
    $token->deleteThis();
    
    $res->redirect("/");
});