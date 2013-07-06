<?php


/*---
Unpin comment
---*/
$router->delete(v0 . "/events/comment/:comment/pin", function () {
    
    $token  = new EventInvite($req->body->event_token);
    if (!$token->key()) throw new TokenException();
    
    if (!is_numeric($req->params->comment)) throw new ParameterException("Invalid comment");
    
    $comment = new EventComment($req->params->comment);
    if ($comment->event->id !== $token->event->id) throw new ParameterException("Invalid comment");
    if ($comment->user->id  !== $token->user->id)  throw new ParameterException("Not authorized");
    
    $comment->pinned = 0;
    $comment->save();
    
    $res->success(array("status"=>"ok"));
    
});




/*---
Pin comment
---*/
$router->post(v0 . "/events/comment/:comment/pin", function () {
    
    $token  = new EventInvite($req->body->event_token);
    if (!$token->key()) throw new TokenException();
    
    if (!is_numeric($req->params->comment)) throw new ParameterException("Invalid comment");
    
    $comment = new EventComment($req->params->comment);
    if ($comment->event->id !== $token->event->id) throw new ParameterException("Invalid comment");
    if ($comment->user->id  !== $token->user->id)  throw new ParameterException("Not authorized");
    
    $comment->pinned = 1;
    $comment->save();
    
    $res->success(array("status"=>"ok"));
    
});


/*---
Delete comment
---*/
$router->delete(v0 . "/events/comment/:comment". function (&$req, &$res) {
    $token  = new EventInvite($req->body->event_token);
    if (!$token->key()) throw new TokenException();
    
    if (!is_numeric($req->params->comment)) throw new ParameterException("Invalid comment");
    
    $comment = new EventComment($req->params->comment);
    if ($comment->event->id !== $token->event->id) throw new ParameterException("Invalid comment");
    if ($comment->user->id  !== $token->user->id)  throw new ParameterException("Not authorized");
       
    $comment->deleteThis();
    $res->success(array("status"=>"ok"));
});




/*---
Post comment
---*/
$router->post(v0 . "/events/comment", function (&$req, &$res) {

    $token  = new EventInvite($req->body->event_token);
    if (!$token->key()) throw new TokenException();
    
    $text = $req->body("text","required");
    if (!$req->isValid()) throw new ParameterException($req->validationErrors);
    
    $comment = new EventComment;
    $comment->user  = $token->user;
    $comment->event = $token->event;
    $comment->text  = $text;
    $comment->save();
    if (!$comment->id) throw new DatabaseException();
    
    $response = $comment->get("id", "text", "pinned", "created_at");
    $response["user"] = $comment->user->info();
    
    $res->success($response);
    
});


/*---
Invite
---*/
$router->post(v0 . "/events/invite", function (&$req, &$res) {

    $token  = new EventInvite($req->body->event_token);
    if (!$token->key()) throw new TokenException();
    
    $invite = new EventInvite;
    $invite->event = $token->event;
    for ($i = 0; $i < count($req->body->invite); $i++) 
        $invite->inviteByChannel($req->body->invite[$i]);
    
    $res->success(array("status"=>"ok"));
    
});




/*---
Event
---*/
$router->post(v0 . "/events", function (&$req, &$res) {

    $access_token = new AccessToken($req->body->access_token);
    if (!$access_token->isValid()) throw new TokenException();
    
    $description = $req->body("description", "required|len[1]");
    
    if (isset($req->body->invite) && !is_array($req->body->invite))
        throw new ParameterException("Invalid argument invite");
    
    if (isset($req->body->deadline) && !Validate::that($req->body->deadline)->isTimestamp()->please())
        throw new ParameterException("Invalid argument deadline");
    
    // host
    $user = new User($access_token->user);
    
    // create event
    $event = new Event();
    $event->user = $user;
    $event->description = $req->body->description;
    if (isset($req->body->deadline)) $event->deadline = $req->body->deadline;
    $event->save();
    if (!$event->id) throw new DatabaseException();
    
    // invite host
    $invite = new EventInvite;
    $invite->event = $event;
    $invite->user  = $user;
    $invite->insert();
    $host_token = $invite->key();
    
    // invite everybody else
    if (isset($req->body->invite))
    {
        $count = count($req->body->invite);
        for ($i = 0; $i < $count; $i++) {
            $invite->inviteByChannel($req->body->invite[$i]);
        }
    }
    
    $response = $event->get("id", "description");
    $response["token"] = $host_token;

    $res->success($response);
});