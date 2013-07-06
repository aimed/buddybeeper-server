<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "SplClassLoader.php";

new SplClassLoader(null, __DIR__ . DIRECTORY_SEPARATOR . "lib");