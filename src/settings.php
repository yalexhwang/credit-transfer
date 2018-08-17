<?php

$creds = include_once(__DIR__ . '/../credentials.php');
// $creds = include_once($_SERVER['DOCUMENT_ROOT'] . '/credentials.php');
$path_mail_templates = $_SERVER['DOCUMENT_ROOT'] . '/mail_templates';
// $path_mail_templates = $_SERVER['DOCUMENT_ROOT'] . '/public/mail_templates';
$root =  $_SERVER['DOCUMENT_ROOT'] . '/public';
$root =  $_SERVER['DOCUMENT_ROOT'];

return [
  'settings' => [
    // set to false in production
    'displayErrorDetails' => true, 
    // Allow web server to send content-length header
    'addContentLengthHeader' => false, 
    'upload_path' => $root . '/uploads',
    'status' => [
      'discard' => 4
    ],

    'ApiAuthMiddleware' => [
      'path_current_user' => '/api/self',
      'cookie_name' => 'tc'
    ],

    'cas' => [
      'version' => 'CAS_VERSION_1_0',
      'host' => 'login.gatech.edu',
      'port' =>  443,
      'uri' => '/cas',
      'session' => true
    ],

    'db' => [
      'charset' => 'utf8',
      'driver' => 'mysql',
      'database' => $creds['mysql-dev']['database'],
      'host' => 'localhost:3306',
      'password' => $creds['mysql-dev']['password'],
      'username' => $creds['mysql-dev']['username']
    ],

    'gted' => [
      'connection_dn' => 'uid=' . $creds['gted']['name'] . ',ou=local accounts,dc=gted,dc=gatech,dc=edu',
      'host' => "ldaps://r.gted.gatech.edu",
      'port' => 636,
      'password' => $creds['gted']['password'],
      'search_dn' => ['uid=', 'ou=accounts,ou=gtaccounts,ou=departments,dc=gted,dc=gatech,dc=edu'],
      'roles' => [
        'staff' => 4,
        'student' => 6
      ]
    ],

    'logger' => [
      'format' => "[%datetime%]\n%channel%(%level_name%): %message%\n%context%\n\n",
      'level' => 'DEBUG',
      'name' => 'TC',
      'path' => __DIR__ . '/../logs/app-back.log'
    ],

    'mailer' => [
      'email' => 'transfercredit@registrar.gatech.edu',
      'subjects' => [
        'turn' => ['[Credit Transfer] Request ID ', ' needs your action'],
        'status' => ['[Credit Transfer] Request ID ', ' status: '],
      ], 
      'templates' => [
        'turn' =>  $path_mail_templates . '/turnUpdated.html',
        'status' => $path_mail_templates . '/statusUpdated.html'
      ]
    ],

    'session' => [
      'name' => 'user'
    ],

    'view' => [
      // 'cache_path' => __DIR__ . '/resources/templates/cached',
      'cache' => false,
      'debug' => true,
      'template_path' => [__DIR__ . '/resources']
    ]
  ]
];
