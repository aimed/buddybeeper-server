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
Vote Date or Activity
---*/
$router->post(v0 . "/events/:type/:id", function (&$req, &$res) {

    $token = new EventInvite($req->body->event_token);
    if (!$token->isValid()) throw new TokenException();
    
    
    switch ($req->params->type) {
        case "activity": $obj = new EventActivity; break;
        case "date":     $obj = new EventDate; break;
        default: throw new ParameterException("Invalid vote type"); break;
    }
    
    $obj->id = $req->params->id;

    if ($obj->event != $token->event->id || $obj->user != $token->user->id) 
        throw new ParameterException("Not authorized");
    
    // vote something
    $vote = new EventVote;
    $vote->user   = $token->user;
    $vote->event  = $token->event;
    $vote->choice = $req->body->id;
    $vote->type   = $req->params->type;
    $vote->insert();
    
    $res->success(array("status" => DB::affectedRows() != 0 ? "ok" : "failed"));
    
});


$router->delete(v0 . "/events/:type/:id", function (&$req, &$res) {

    $token = new EventInvite($req->body->event_token);
    if (!$token->isValid()) throw new TokenException();
    
    switch ($req->params->type) {
        case "activity": $obj = new EventActivity; break;
        case "date":     $obj = new EventDate; break;
        default: throw new ParameterException("Invalid vote type"); break;
    }
    
    $obj->id = $req->params->id;

    if ($obj->event != $token->event->id || $obj->user != $token->user->id) 
        throw new ParameterException("Not authorized");
    
    $vote = new EventVote;
    $vote->delete(array(
        "type"   => $req->params->type,
        "user"   => $token->user,
        "choice" => $req->params->id
    )); 
    
    $res->success(array("status" => DB::affectedRows() > 0 ? "ok" : "failed"));
    
});




/*---
Create Date or Activity
---*/
$router->post(v0 . "/events/:type", function (&$req, &$res) {
    
    $token = new EventInvite($req->body->event_token);
    if (!$token->isValid()) throw new TokenException();
    
    switch ($req->params->type) {
        case "date":
            $start = $req->body("start", "required|string|isTimestamp");
            $end   = $req->body("end", "string|isTimestamp");
            if (!$req->isValid()) throw new ParameterException($req->validationErrors);
            
            $obj = new EventDate;
            $obj->user  = $token->user;
            $obj->event = $token->event;
            $obj->start = $start;
            $obj->end   = $end;
            $obj->save();
            
            $response = $obj->get("id", "start", "end");
            break;
        
        case "activity":
            $activity = $req->body("activity", "required|string|len[1]");
            if (!$req->isValid()) throw new ParameterException($req->validationErrors);
            
            $obj = new EventActivity;
            $obj->user     = $token->user;
            $obj->event    = $token->event;
            $obj->activity = $activity;
            $obj->save();
            
            $response = $obj->get("id", "activity");
            
            break;
        
        default: throw new ParameterException("Invalid vote type"); break;
    }
    
    $vote = new EventVote;
    $vote->user   = $token->user;
    $vote->type   = $req->params->type;
    $vote->choice = $obj->id;
    $vote->insert();
    
    $response["votes"] = array($token->user->id);    
    $res->success($response);
    
});



/*---
Event
---*/
$router->post(v0 . "/events", function (&$req, &$res) {

    $access_token = new AccessToken($req->body->access_token);
    if (!$access_token->isValid()) throw new TokenException();
    
    $description = $req->body("description", "required|len[1]");
    $deadline    = $req->body("deadline",    "isTimestamp");
    if (isset($req->body->invite) && !is_array($req->body->invite))
        throw new ParameterException("Invalid argument invite");
    
    if (!$req->isValid()) throw new ParameterException($req->validationErrors);
    
    // host
    $user = new User($access_token->user);
    
    // create event
    $event = new Event();
    $event->user        = $user;
    $event->description = $description;
    $event->deadline    = $req->deadline;
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

$router->get(v0 . "/events", function (&$req, &$res) {
    $token = new EventInvite($req->body->event_token);
    if (!$token->isValid()) throw new TokenException();
    $res->success($token->event->get(
        "id", "description", "dates", "activities", "invites", 
        "final_date", "final_location", "final_activity", "deadline", "created_at"
    ));
});