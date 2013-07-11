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
    $req->body->access_token = "extjNBulUSBKCbalNcxEM4DgZyq/XZPZC39nlKRemxF3xKgST8OIudPcBOBjuRjz";
    $req->body->event_token  = "ec3N1PNntWkmVgMY8arnYb64WJpc33gCyE2uG1WIY5C_k-j-dLP9fx4BVhohwQje";
    $req->body->email = "test@localhost";
    $req->body->password = "password";
    $req->body->first_name = "Max";
    $req->body->last_name  = "T";
    $req->body->description = "My Event";
    $req->body->invite = array("test@localhost");
    /* /HARDCODED*/
    
    $access_token = new AccessToken($req->body->access_token);
    if (!$access_token->isValid()) throw new TokenException();
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