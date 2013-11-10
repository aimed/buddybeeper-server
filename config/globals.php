<?php
// Filepaths
Utils::define("ROOT_DIR", realpath("../"));
Utils::define("BASE_URL", $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"]);
Utils::define("BUDDYBEEPER_WEB_URL", "http://local.buddybeeper.dev");
Utils::define("BUDDYBEPPER_API_URL", "http://api.local.buddybeeper.dev");

// Mailer
Utils::define("TEMPLATE_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "_api" . DIRECTORY_SEPARATOR . "templates");
Utils::define("MAIL_DEFAULT_FROM_ADDRESS", "noreply@buddybeeper.net");

// Vault
Utils::define("VAULT_SECRET", "NwatEwfN3y5C2DEUAqwbl20v1SwuxLQ9R5gtbOZy");

// Uploader
Utils::define("UPLOADER_DEFAULT_DIR", ROOT_DIR . DIRECTORY_SEPARATOR . "_web" . DIRECTORY_SEPARATOR . "usercontent");