<?php
$I = new ApiGuy($scenario);

$I->wantTo("Generate an access token");

$secret = $I->grabFromDatabase("clients", "secret", array("id" => 1));

$I->haveHttpHeader("Content-Type","application/x-www-form-urlencoded");
$I->sendPost("/auth/token", array("client_id" => 1, "client_secret" => $secret, "username" => "maximilian@localhost", "password" => "password"));
$I->seeResponseCodeIs(200);
$I->seeResponseContains("access_token");