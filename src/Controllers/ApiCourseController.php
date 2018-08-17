<?php
namespace App\Controllers;

use App\Services\CourseService;
use App\Services\LabService;
use Slim\Http\UploadedFile;

class ApiCourseController extends Controller {

  private $courseSvc;
  private $labSvc;
  private $settings;

  public function __construct($logger, $settings) {
    parent::__construct($logger);
    $this->courseSvc = new CourseService($logger, $settings);
    $this->labSvc = new LabService($logger, $settings);
  }

  public function get($req, $res, $args) {
    $query = $req->getQueryParams();
    $this->logger()->info('Gettingg a course', array('courseData' => $query));
    return $res->withJson($this->courseSvc->get($query));
  }

  public function add($req, $res, $args) {
    $data = $req->getParsedBody();
    $this->logger()->info('Adding a course', array('courseData' => $data));
    return $res->withJson($this->courseSvc->add($data));
  }

  public function edit($req, $res, $args) {
    $data = $req->getParsedBody();
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('Editing a course', [$data, $id]);
    return $res->withJson($this->courseSvc->edit($data, $id));
  }

  public function delete($req, $res, $args) {
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('Deleting a course', array('id' => $id));
    return $res->withJson($this->courseSvc->delete($id));
  }

  // For documents
  public function receive($req, $res, $args) {
    $files = $req->getUploadedFiles();
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('Uploaded file receieved', [$files, $id]);
    return $res->withJson($this->courseSvc->addDocument($files, $id));
  }

  public function remove($req, $res, $args) {
    $id = $req->getQueryParams()['id'];
    $types = $req->getQueryParams()['type'];
    $this->logger()->info('removing docs', [$id, $types]);
   return $res->withJson($this->courseSvc->deleteDocument($types, $id));

  }

  public function getHistory($req, $res, $args) {
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('Getting course evalHistory', array('id' => $id));
    return $res->withJson($this->courseSvc->getEvaluationHistory($id));
  }

  public function getGtCourseSuggestions($req, $res, $args) {
    $id = $req->getQueryParams()['id'];
    $this->logger()->info('Getting GT Course Suggestions', array('id' => $id));
    return $res->withJson($this->courseSvc->getGtCourseSuggestions($id));
  }

}
