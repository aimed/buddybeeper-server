<?php


/*---
Unpin comment
---*/
$router->delete(v0 . "/events/comment/:comment/pin", function () {
    
    $token  = new EventInvite($req->headers("x-event-token"));
    if (!$token->isValid()) throw new TokenException();
    
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
    
    $token  = new EventInvite($req->headers("x-event-token"));
    if (!$token->isValid()) throw new TokenException();
    
    
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
$router->delete(v0 . "/events/comment/:comment", function (&$req, &$res) {
    $token  = new EventInvite($req->headers("x-event-token"));
    if (!$token->isValid()) throw new TokenException();
    
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
	
    $token  = new EventInvite($req->headers("x-event-token"));
    if (!$token->isValid()) throw new TokenException("Invalid event token" . $req->headers("x-event-token"));

    $text = $req->body("text","required");
    if (!$req->isValid()) throw new ParameterException($req->validationErrors);
    
    $comment = new EventComment;
    $comment->user  = $token->user;
    $comment->event = $token->event;
    $comment->text  = Utils::htmlsafe($text);
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
    $token  = new EventInvite($req->headers("x-event-token"));
    if (!$token->isValid()) throw new TokenException();
    
    if (empty($req->body->invites)) throw new ParameterException("Missing required argument invites");
    
    $invites = (array) $req->body->invites;
    $invite = new EventInvite;
    $invite->event = $token->event;
    for ($i = 0; $i < count($invites); $i++)  $invite->inviteByChannel($invites[$i]);
    
    $res->success(array("status"=>"ok","invites"=>$invite->event->invites));
    
});




/*---
Vote Date or Activity
---*/
$router->post(v0 . "/events/:type/:id", function (&$req, &$res) {

    $token = new EventInvite($req->headers("x-event-token"));
    if (!$token->isValid()) throw new TokenException("Invalid event token");
        
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
    $vote->choice = $req->params->id;
    $vote->type   = $req->params->type;
    $vote->insert();
    
    $res->success(array("status" => DB::affectedRows() != 0 ? "ok" : "failed"));
    
});


$router->delete(v0 . "/events/:type/:id", function (&$req, &$res) {
	
	
	$token = new EventInvite($req->query("token"));
	if (!$token->isValid()) throw new TokenException("Invalid event token " . $req->headers("x-event-token"));
    
    switch ($req->params->type) {
        case "activity": $obj = new EventActivity; break;
        case "date":     $obj = new EventDate; break;
        default: throw new ParameterException("Invalid vote type"); break;
    }
    
    $obj->id = $req->params->id;
	
	// @TODO: YEP
    //if ($obj->event != $token->event->id || $obj->user != $token->user->id) 
    //    throw new ParameterException("Not authorized");
    
    $vote = new EventVote;
    $vote->delete(array(
        "type"   => $req->params->type,
        "user"   => $token->user,
        "choice" => $obj->id
    )); 
    
    $res->success(array("status" => DB::affectedRows() > 0 ? "ok" : "failed"));
    
});




/*---
Create Date or Activity
---*/
$router->post(v0 . "/events/:type", function (&$req, &$res) {
    
    $token = new EventInvite($req->headers("x-event-token"));
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
            $activity = $req->body("name", "required|string|len[1]");
            if (!$req->isValid()) throw new ParameterException($req->validationErrors);
            
            $obj = new EventActivity;
            $obj->user     = $token->user;
            $obj->event    = $token->event;
            $obj->name     = $activity;
            $obj->save();
            
            $response = $obj->get("id", "name");
            
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

    $access_token = new AccessToken($req->headers("x-access-token"));
    if (!$access_token->isValid()) throw new TokenException();
    
    $title 		 = $req->body("title", "required|len[1]");
    $description = $req->body("description", "len[1]");
    $deadline    = $req->body("deadline",    "isTimestamp");
    if (isset($req->body->invites) && !is_array($req->body->invites))
        throw new ParameterException("Invalid argument invite");
    
    if (!$req->isValid()) throw new ParameterException($req->validationErrors);
    
    // host
    $user = new User($access_token->user);
    
    // create event
    $event = new Event;
    $event->user        = $user;
    $event->title       = $title;
    $event->description = $description;
    $event->deadline    = $deadline;
    $event->save();
    
    if (!$event->id) throw new DatabaseException();
    
    // invite host
    $invite = new EventInvite;
    $invite->event = $event;
    $invite->user  = $user;
    $invite->insert();
    
    $host_token = $invite->event_token;
    
    // invite everybody else
    if (isset($req->body->invites))
    {
        $count = count($req->body->invites);
        for ($i = 0; $i < $count; $i++) {
            $invite->inviteByChannel($req->body->invites[$i]);
        }
    }
    
    $response = $event->get("id", "title", "description");
    $response["token"] = $host_token;

    $res->success($response);

});

$router->get(v0 . "/events", function (&$req, &$res) {
    $token = new EventInvite($req->headers("x-event-token"));
    if (!$token->isValid()) throw new TokenException();
    $response = $token->event->get(
        "id", "description", "dates", "activities", "invites", 
        "final_date", "final_location", "final_activity", "deadline", "created_at",
        "comments"
    );
    $response["user"] = $token->user->info();
    $res->success($response);
});