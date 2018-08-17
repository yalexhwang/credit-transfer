<?php
namespace App\Controllers;

use App\Services\SchoolService;

class ApiSchoolController extends Controller {

  public function __construct($logger) {
    parent::__construct($logger);
    $this->schoolSvc = new SchoolService($logger);
  }

  public function get($req, $res, $args) {
    $query = $req->getQueryParams();
    $this->logger()->info('Retrieving school(s)', array('query' => $query));
    return $res->withJson($this->schoolSvc->get($query));
  }

  public function getList($req, $res, $args) {
    if ($args['list_for'] == 'state') {
      $result = $this->schoolSvc->getStateList();
    }
    if ($args['list_for'] == 'country') {
      $result = $this->schoolSvc->getCountryList();
    }
    return $res->withJson($result);
  }

  public function add($req, $res, $args) {
    $data = $req->getParsedBody();
    $this->logger()->info('adding a school', [$data]);
    return $res->withJson($this->schoolSvc->add($data));
  }

  public function edit($req, $res, $args) {
    $data = $req->getParsedBody();
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('editing a school', [$data, $id]);
    return $res->withJson($this->schoolSvc->edit($data, $id));
  }

  public function delete($req, $res, $args) {
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('deleting a school', [$id]);
    return $res->withJson($this->schoolSvc->delete($id));
  }

}
