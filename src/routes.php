<?php
use App\Middleware\HomeMiddleware;
use App\Middleware\ApiAuthMiddleware;

$homeMW = new HomeMiddleware($container->session, $container->logger);
$apiMW = new ApiAuthMiddleware($container->session, $container->logger, $container->cas, $container->settings);

$app->get('/', HomeCtrl::class . ':landing')->add($homeMW);
$app->get('/home', HomeCtrl::class . ':home')->add($homeMW);
$app->get('/login', LoginCtrl::class . ':login')->add($homeMW);
$app->get('/logout', LoginCtrl::class . ':logout')->add($homeMW);

// API
$app->group('/api', function() {
  $this->group('/self', function() {
    $this->map(['OPTIONS'], '', ApiUserCtrl::class);
    $this->get('', ApiUserCtrl::class . ':getCurrentUser');
  });

  $this->group('/request', function() {
    $this->map(['OPTIONS'], '', ApiRequestCtrl::class);
    $this->group('', function() {
      $this->get('', ApiRequestCtrl::class . ':get');
      $this->get('/new', ApiRequestCtrl::class . ':getNew');
      $this->get('/view/{requestID}', ApiRequestCtrl::class . ':getView');
      $this->post('', ApiRequestCtrl::class . ':add');
      $this->put('', ApiRequestCtrl::class . ':edit');
      $this->delete('', ApiRequestCtrl::class . ':delete');
    });
    $this->group('/upload', function() {
      $this->post('', ApiRequestCtrl::class . ':receive');
      $this->delete('', ApiRequestCtrl::class . ':remove');
    });
  });

  $this->group('/action', function() {
    $this->map(['OPTIONS'], '', ActionCtrl::class);
    $this->get('', ActionCtrl::class . ':get');
    $this->put('/log/{logTemplateID}', ActionCtrl::class . ':log');
    $this->put('/evaluation/{requestID}', ActionCtrl::class . ':evaluate');
    $this->post('/{actionID}', ActionCtrl::class . ':take');
  });
  
  $this->group('/mail', function () {
    $this->map(['OPTIONS'], '', ActionCtrl::class);
    $this->get('/turn', ActionCtrl::class . ':sendTurnMail');
    $this->get('/status', ActionCtrl::class . ':sendStatusMail');
  });

  $this->group('/contents', function() {
    $this->map(['OPTIONS'], '', ApiContentsCtrl::class);
    $this->get('', ApiContentsCtrl::class . ':getUIcontents');
    $this->get('/{item}', ApiContentsCtrl::class . ':getContentsItem');
  });

  $this->group('/gtcourse', function() {
    $this->map(['OPTIONS'], '', ApiGtCourseCtrl::class);
    $this->get('', ApiGtCourseCtrl::class . ':get'); 
  });

  // User-related
  $this->group('/user', function() {
    $this->map(['OPTIONS'], '', ApiUserCtrl::class);
    $this->get('', ApiUserCtrl::class . ':get');
    $this->get('/self', ApiUserCtrl::class . ':getCurrentUser');
    $this->post('', ApiUserCtrl::class . ':add');
    $this->put('', ApiUserCtrl::class . ':edit');
    $this->delete('', ApiUserCtrl::class . ':delete');
  });

  // Course-related
  $this->group('/course', function() {
    $this->map(['OPTIONS'], '', ApiCourseCtrl::class);
    $this->group('', function() {
      $this->get('', ApiCourseCtrl::class . ':get');
      $this->post('/check', ApiCourseCtrl::class . ':checkDuplidate');
      $this->post('', ApiCourseCtrl::class . ':add');
      $this->put('', ApiCourseCtrl::class . ':edit');
      $this->delete('', ApiCourseCtrl::class . ':delete');
    });
    $this->group('/upload', function() {
      $this->post('', ApiCourseCtrl::class . ':receive');
      $this->delete('', ApiCourseCtrl::class . ':remove');
    });
  });

  $this->group('/school', function() {
    $this->map(['OPTIONS'], '', ApiSchoolCtrl::class);
    $this->get('', ApiSchoolCtrl::class . ':get');
    $this->post('', ApiSchoolCtrl::class . ':add');
    $this->put('', ApiSchoolCtrl::class . ':edit');
    $this->delete('', ApiSchoolCtrl::class . ':delete');
  });
})->add($apiMW);
