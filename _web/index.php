<?php

include "../autoload.php";
include "../config/globals.php";
include "../config/database.php";

new SplClassLoader(null, __DIR__);
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
		setcookie("rft", Vault::encrypt($bb->refresh_token, VAULT_SECRET), time() + 3600*24*30, "/", "buddybeeper.dev");
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
		$cookie->expires_in(1, "month");
		$cookie->set();
	}
	
	$res->json($response);
});



$router->get("/logout", function (&$res) {
	$cookie = new Cookie("rft");
	$cookie->delete();
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



$router->get("/*", function (&$req, &$res) {
	// Check if mobile site was requested.
	$env = substr($_SERVER["SERVER_NAME"], 0, 2) === "m." ? 
		"_mobile" : 
		"_desktop";
	include __DIR__ . DIRECTORY_SEPARATOR . $env . DIRECTORY_SEPARATOR . "index.html";
});