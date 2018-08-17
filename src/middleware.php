<?php
namespace App;

// add additional options ex) httponly, domain ...
$app->add(new \Slim\Middleware\Session([
  'name' => 'tc',
  'autorefresh' => true,
  'lifetime' => '1 hour'
]));

$app->add(new \Tuupola\Middleware\Cors([
  "origin" => ["http://localhost:4200", "http://slim-0.localhost.gatech.edu", "*"],
  "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"],
  "headers.allow" => ["Connection", "Origin", "Content-type", "Authorization"],
  "headers.expose" => ["Set-cookie"],
  "credentials" => true,
  "cache" => 0,
]));

