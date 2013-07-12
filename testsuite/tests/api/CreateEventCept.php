<?php
$I = new ApiGuy($scenario);

$I->wantTo("create an event");

$token = $I->grabFromDatabase("client_access_tokens", "access_token", array("client" => 1, "user" => 1));
$I->haveHttpHeader("x-access-token", "" . $token);
$I->haveHttpHeader("Content-Type","application/x-www-form-urlencoded");

$I->sendPost("/events", array("description" => "Whoopwhoop"));

$I->seeResponseCodeIs(200);
$I->seeResponseContains("response");
