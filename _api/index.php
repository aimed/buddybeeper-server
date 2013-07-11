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
    $req->body = (object)array();
    $req->params = (object)array();
    $req->headers->{"X-ACCESS-TOKEN"} = "extjNBulUSBKCbalNcxEM4DgZyq/XZPZC39nlKRemxF3xKgST8OIudPcBOBjuRjz";
    /* /HARDCODED*/

	
});



/*-----
Set up exception handling
-----*/
set_exception_handler(function ($e) use (&$router) {
    $response = $router->getResponse();
    $response->json($e->get());
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