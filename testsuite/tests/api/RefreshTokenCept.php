<?php
$I = new ApiGuy($scenario);

$I->wantTo("use a refresh token");
$token = $I->grabFromDatabase("client_refresh_tokens", "refresh_token", array("client" => 1, "user" => 1));
$I->haveHttpHeader("Content-Type","application/x-www-form-urlencoded");
$I->sendPost("/auth/refresh", array("refresh_token" => $token));
$I->seeResponseCodeIs(200);
$I->seeResponseContains("access_token");