<?php
namespace App;

use Illuminate\Database\Capsule\Manager;
use \Slim\Views\Twig;
use App\Handlers\SessionHandler;
use App\Handlers\LogHandler;
use phpCAS;

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\ActionController;
use App\Controllers\ApiContentsController;
use App\Controllers\ApiCourseController;
use App\Controllers\ApiGtCourseController;
use App\Controllers\ApiRequestController;
use App\Controllers\ApiSchoolController;
use App\Controllers\ApiUserController;


// DIC configuration
$container = $app->getContainer();

$container['cas'] = function($c) {
  $setting = $c['settings']['cas'];
  $cas = new phpCAS();
  $cas->setDebug();
  $cas->client(CAS_VERSION_1_0, $setting['host'], $setting['port'], $setting['uri']);
  // comment out once in production
  $cas->setNoCasServerValidation();
  return $cas;
};

$capsule = new Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($c) {
  return $capsule;
};

$container['logger'] = function($c) {
  $setting = $c['settings']['logger'];
  return new LogHandler($setting['name'], $setting['format'], $setting['path'], $setting['level']);
};

$container['session'] = function($c) {
  return new SessionHandler($c['settings']['session']['name']);
};

$container['view'] = function($c) {
  return new Twig(
    $c['settings']['view']['template_path'], [
      'cache' => $c['settings']['view']['cache'], 
      'debug' => $c['settings']['view']['debug']
    ]);
};

$container['HomeCtrl'] = function($c) {
  return new HomeController($c->logger, $c->view);
};

$container['LoginCtrl'] = function($c) {
  return new LoginController($c->logger, $c->cas, $c->settings);
};

$container['ActionCtrl'] = function($c) {
  return new ActionController($c->logger, $c->settings);
};

$container['ApiContentsCtrl'] = function($c) {
  return new ApiContentsController($c->logger);
};

$container['ApiCourseCtrl'] = function($c) {
  return new ApiCourseController($c->logger, $c->settings);
};

$container['ApiGtCourseCtrl'] = function($c) {
  return new ApiGtCourseController($c->logger);
};

$container['ApiRequestCtrl'] = function($c) {
  return new ApiRequestController($c->logger, $c->settings);
};

$container['ApiSchoolCtrl'] = function($c) {
  return new ApiSchoolController($c->logger);
};

$container['ApiUserCtrl'] = function($c) {
  return new ApiUserController($c->logger);
};


