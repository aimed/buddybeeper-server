<?php

/*-----
Include dependencies and register classloaders
-----*/
include "../autoload.php";
include "../config/database.php";
include "../config/globals.php";

new SplClassLoader(null, __DIR__ . DIRECTORY_SEPARATOR . "models");
new SplClassLoader(null, __DIR__ . DIRECTORY_SEPARATOR . "exceptions");



/*-----
API Versioning
-----*/
define("v0", "/v0");




/*-----
Configure DB
-----*/
DB::connect("mysql:host=".MYSQL_HOST.";dbname=".MYSQL_DATABASE, MYSQL_USERNAME, MYSQL_PASSWORD);




/*-----
Prepare routing
-----*/
$router = new Router();
$router->route($_SERVER["REQUEST_URI"]);
$router->get("/test", function (&$req, &$res) {
    /* HARDCODED */
//    $req->body = (object)array();
//    $req->params = (object)array();
//    $req->headers = (object)array();
//    $req->headers->{"x-access-token"} = "extjNBulUSBKCbalNcxEM4DgZyq/XZPZC39nlKRemxF3xKgST8OIudPcBOBjuRjz";
//    $req->headers->{"x-event-token"} = "ec3N1PNntWkmVgMY8arnYb64WJpc33gCyE2uG1WIY5C_k-j-dLP9fx4BVhohwQje";
    /* /HARDCODED*/
    $i = new Image(ROOT_DIR."/_web/assets/img/IMG_123.jpg");
    $i->toFormat(Image::jpg);
    $i->crop(100);
    $i->save(ROOT_DIR."/_web", "test");
	echo("okay");    
});
$router->post("/test", function (&$req, &$res) {
	
	$token = new AccessToken($req->body("access_token"));
	if (!$token->isValid()) throw new TokenException();
	
	// upload image
	$uploader = new Uploader("profile_image");
	$uploader->makeUnique();
	//$uploader->allowMimeType();
	if (!$uploader->save()) throw new ParameterException($uploader->_errors);
	
	// set profile image
	$user = new User($token->user);
	$user->is_default_image = 0;
	$user->profile_image = "/usercontent/" . $uploader->filename;
	$user->update();
	
	// create thumbnail
	$thumb = new Image($uploader->filepath);
	$thumb->crop(120);
	$thumb->toFormat(Image::jpg);
	$thumb->save(ROOT_DIR . "/_web/usercontent/tumb", $user->id . "_120_120");
	
	$res->success($user->info());
    //var_dump($uploader,$_FILES);
});




/*---
Allow API calls via JS
---*/
$router->uses(function (&$req, &$res) {
	//if (!$req->headers("ORIGIN")) return; @TODO: yeah, what.
	$res->header("Access-Control-Allow-Origin","*");
	$res->header("Access-Control-Allow-Headers","X-Requested-With, X-Access-Token, X-Event-Token, Content-Type");
	$res->header("Access-Control-Allow-Methods","GET, POST, DELETE");
	if ($req->requestMethod === "OPTIONS") 
	{
		$res->send("",200);
		exit(0);
	}
});




/*-----
Set up exception handling
-----*/
set_exception_handler(function ($e) use (&$router) {
    $response = $router->getResponse();
    $response->json($e->get(), $e->responseCode);
});




/*-----
Routing
-----*/
include "routes/auth.php";
include "routes/user.php";
include "routes/event.php";




/*---
404 Fallback
---*/
$router->when("/*", function (&$req, &$res) {
	$res->send("404 Not Found",404);
});