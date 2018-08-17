<?php
require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('America/New_York');

session_name('tc');
session_start();

$settings = require __DIR__ . '/../src/settings.php';
// $settings = require __DIR__ . '/src/settings.php';

$app = new \Slim\App($settings);

require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/middleware.php';
require __DIR__ . '/../src/routes.php';
// production env
// require __DIR__ . '/src/dependencies.php';
// require __DIR__ . '/src/middleware.php';
// require __DIR__ . '/src/routes.php';

$app->run();
