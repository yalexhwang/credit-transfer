<?php
namespace App\Controllers;

use App\Services\ContentsService;

class ApiContentsController extends Controller {

  private $contSvc;
  
  public function __construct($logger) {
    parent::__construct($logger);
    $this->contSvc = new ContentsService($logger);  
  }

  public function getUIcontents($req, $res, $args) {
    $component = $req->getQueryParams()['component'];
    return $res->withJson($this->contSvc->getUIcontents($component));
  }

  public function getContentsItem($req, $res, $args) {
    $query = $req->getQueryParams();
    $item = $args['item'];
    if ($item === 'action') {
      $result = $this->contSvc->getAction();
    }
    if ($item === 'country') {
      $result = $this->contSvc->getCountry();
    }
    if ($item === 'department') {
      $result = $this->contSvc->getDepartment($query);
    }
    if ($item === 'staff') {
      $result = $this->contSvc->getStaff($query);
    }
    if ($item === 'state') {
      $result = $this->contSvc->getState();
    }
    if ($item === 'status') {
      $result = $this->contSvc->getStatus();
    }
    if ($item === 'step') {
      $result = $this->contSvc->getStep();
    }
    if ($item === 'subject') {
      $result = $this->contSvc->getSubject();
    }
    return $res->withJson($result);
  }

}
