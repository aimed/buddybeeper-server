<?php
//$I = new ApiGuy($scenario);
//
//$I->wantTo("get event");
//
//$access_token = $I->grabFromDatabase("client_access_tokens", "access_token", array("client" => 1, "user" => 1));
//$event_token = $I->grabFromDatabase("event_invites", "event_token", array("event" => 1, "user" => 1));
//$I->haveHttpHeader("x-access-token", "" . $access_token);
//$I->haveHttpHeader("x-event-token", "" . $event_token);
//$I->haveHttpHeader("Content-Type","application/x-www-form-urlencoded");
//
//$I->sendGet("/events/get");
//
//$I->seeResponseCodeIs(200);
//$I->seeResponseContains("response");
