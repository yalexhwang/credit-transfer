<?php
namespace App\Controllers;

use App\Services\GtCourseService;

class ApiGtCourseController extends Controller {

  public function __construct($logger) {
    parent::__construct($logger);
    $this->courseSvc = new GtCourseService($logger);
  }

  public function get($req, $res, $args) {
    $query = $req->getQueryParams();
    $this->logger()->info('Getting GtCourse(s)', array('courseData' => $query));
    return $res->withJson($this->courseSvc->get($query));
  }

  public function add($req, $res, $args) {
    $data = $req->getParsedBody();
    $this->logger()->info('Adding a GtCourse', array('courseData' => $data));
    return $res->withJson($this->courseSvc->add($data));
  }

  public function edit($req, $res, $args) {
    $data = $req->getParsedBody();
    $query = $req->getQueryParams();
    $this->logger()->info('Editing a GtCourse', array('courseData' => $data, 'query' => $query));
    return $res->withJson($this->courseSvc->edit($data, $query));
  }

  public function delete($req, $res, $args) {
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('Deleting a GtCourse', array('id' => $id));
    return $res->withJson($this->courseSvc->delete($id));
  }

}
