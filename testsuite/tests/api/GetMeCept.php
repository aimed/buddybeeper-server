<?php
$I = new ApiGuy($scenario);

$I->wantTo("get me");

$token = $I->grabFromDatabase("client_access_tokens", "access_token", array("client" => 1, "user" => 1));
$I->haveHttpHeader("x-access-token", "" . $token);
$I->haveHttpHeader("Content-Type","application/x-www-form-urlencoded");

$I->sendGet("/users/me");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains("response");
$I->seeResponseContains("first_name");
$I->seeResponseContains("events");