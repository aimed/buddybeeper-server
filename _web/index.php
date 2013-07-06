<?php

include "../autoload.php";
include "../config/database.php";

$router = new Router();
$router->route($_SERVER["REQUEST_URI"]);

$router->get("/*", function (&$req, &$res) {
    $res->send("Hi");
});