<?php

include "../autoload.php";
include "../config/globals.php";
include "../config/database.php";

$router = new Router();
$router->route($_SERVER["REQUEST_URI"]);



define("CLIENT_SECRET", "f4wOVg9nBvLz2vprSa5T00Mh8986eXD7LG95pPBf7ctgR7IOo3qtjOT4Rys99cp1");



// @TODO: move to htacces ?
$router->get("/usercontent/thumb/*", function (&$req, &$res) {
	$res->redirect("/usercontent/thumb/default.png");
	exit(0);
});



$router->uses(function (&$req, &$scope) {
	$scope["rft"] = Vault::decrypt($req->cookies("rft"), VAULT_SECRET);
	$scope["act"] = Vault::decrypt($req->cookies("act"), VAULT_SECRET);
	$scope = (object) $scope;
}, array("req", "scope"));



$router->get("/test", function (&$req, &$res, &$scope) {
	$bb = new buddybeeper("1",CLIENT_SECRET);
	var_dump($bb->signup("test2@localhost","password","max","t"));
}, "req res scope");



$router->post("/test", function (&$req, &$res) {
});



$router->post("/login", function (&$req, &$res) {
	$bb = new buddybeeper("1",CLIENT_SECRET);
	$response = $bb->getRefreshToken($req->body("email"),$req->body("password"));
	
	if (isset($response->response)) {
		$cookie = new Cookie("rft", Vault::encrypt($bb->refresh_token, VAULT_SECRET));
		$cookie->expires_in(1, "months");
		$cookie->set();
	}
	
	$res->json($response);
});



$router->post("/register", function (&$req, &$res) {
	$bb = new buddybeeper("1",CLIENT_SECRET);
	$response = $bb->signup(
		$req->body("email"),
		$req->body("password"),
		$req->body("first_name"),
		$req->body("last_name")
	);
	
	if (isset($response->response->status) && $response->response->status == "ok") {
		$cookie = new Cookie("rtf", Vault::encrypt($bb->refresh_token, VAULT_SECRET));
		$cookie->expires_in(1, "months");
		$cookie->set();
	}
	
	$res->json($response);
});



$router->get("/logout", function (&$res) {
	$cookie = new Cookie("rft");
	$cookie->delete();
	echo 'what';
	die('');
	$res->redirect("/");
}, "res");



$router->get("/ping", function (&$req, &$res, &$scope) {
	if (!$scope->rft) {
		return $res->json(null);
	}
	
	$bb = new buddybeeper("1",CLIENT_SECRET);
	$bb->refresh_token = $scope->rft;
	$response = $bb->getAccessToken();

	$res->json($response);
	
},"req res scope");



$router->get("/_mobile/*", function (&$req, &$res) {
	$res->send("",404);
});



$router->get("/_desktop/*", function (&$req, &$res) {
	$res->send("",404);
});



$router->get("/*", function (&$req, &$res) {
	// Check if mobile site was requested.
	// some pointless comment
	$env = substr($_SERVER["SERVER_NAME"], 0, 2) === "m." ?
		"_mobile" :
		"_desktop";
	include __DIR__ . DIRECTORY_SEPARATOR . $env . DIRECTORY_SEPARATOR . "index.html";
});