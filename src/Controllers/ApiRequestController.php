<?php
namespace App\Controllers;

use App\Services\RequestService;
use App\Models\Request;

class ApiRequestController extends Controller {

  private $settings;
  
  public function __construct($logger, $settings) {
    parent::__construct($logger);
    $this->reqSvc = new RequestService($logger, $settings);
  }

  public function get($req, $res, $args) {
    $query = $req->getQueryParams();
    return $res->withJson($this->reqSvc->get($this->getSelf(), $query));
  }

  public function getNew($req, $res, $args) {
    $this->logger()->info('ApiRequestCtrl:getNew');
    return $res->withJson($this->reqSvc->getNew($this->getSelf()));
  }

  public function getView($req, $res, $args) {
    $id = $args['requestID'];
    $this->logger()->info('ApiRequestCtrl:getView', [$id]);
    return $res->withJson($this->reqSvc->getView($this->getSelf(), $id));
  }

  public function edit($req, $res, $args) {
    $data = $req->getParsedBody();
    $this->logger()->info('Editing a request', array('request' => $data));
    return $res->withJson($this->reqSvc->edit($data));
  }

  public function delete($req, $res, $args) {
    $data = $req->getParsedBody();
    $query = $req->getQueryParams();
    $this->logger()->info('Deleting a request',[$query]);
    return $res->withJson($this->reqSvc->delete($query));
  }

  // For documents
  public function receive($req, $res, $args) {
    $this->logger()->info('reqCtrl:receieve');
    $files = $req->getUploadedFiles();
    $courseID = $req->getQueryParams()['id'];
    $this->logger()->info('Uploaded file receieved', [$files, $courseID]);
    return $res->withJson($this->reqSvc->addCourseDocument($files, $courseID));
  }

  public function remove($req, $res, $args) {
    $id = $req->getQueryParams()['id'];
    $types = $req->getQueryParams()['type'];
    $this->logger()->info('removing docs', [$id, $types]);
   return $res->withJson($this->reqSvc->removeCourseDocument($types, $id));
  }

}
