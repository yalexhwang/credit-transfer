<?php
namespace App\Controllers;

class ApiUserController extends Controller {

  public function __construct($logger) {
    parent::__construct($logger);
  }

  public function getCurrentUser($req, $res, $args) {
    $self = $this->getSelf();
    $self->getRelations();
    $this->logger()->info('ApiUserCtrl:getCurrentUser', [$self]);
    return $res->withJson($self);
  }

  public function get($req, $res, $args) {
    $query = $req->getQueryParams();
    $this->logger()->info('ApiUserCtrl:get', [$query]);
    $result = $this->userSvc()->get($query);
    return $res->withJson($result);
  }

  public function add($req, $res, $args) {
    $data = $req->getParsedBody();
    $this->logger()->info('ApiUserCtrl:add', [$data]);
    return $res->withJson($this->userSvc()->add($data));
  }

  public function edit($req, $res, $args) {
    $data = $req->getParsedBody();
    $query = $req->getQueryParams();
    $this->logger()->info('ApiUserCtrl:edit', [$data, $query]);
    return $res->withJson($this->userSvc()->edit($data, $query));
  }

  public function delete($req, $res, $args) {
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('ApiUserCtrl:delete', [$id]);
    return $res->withJson($this->userSvc()->delete($id));
  }


}
