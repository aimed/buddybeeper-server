<?php
// Filepaths
Utils::define("ROOT_DIR", realpath("../"));
Utils::define("BASE_URL", $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"]);
Utils::define("BUDDYBEEPER_WEB_URL", "http://buddybeeper.dev");
Utils::define("BUDDYBEPPER_API_URL", "http://api.buddybeeper.dev");

// Mailer
Utils::define("TEMPLATE_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "templates");

// Vault
Utils::define("VAULT_SECRET", "");