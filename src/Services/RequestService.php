<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use App\Services\CourseService;
use App\Services\Internal\MailService;
use App\Models\Request;

class RequestService extends Service {

  private $statusDiscard;

  public function __construct($logger, $settings) {
    parent::__construct($logger);
    $this->courseSvc = new CourseService($logger, $settings);
    $this->statusDiscard = $settings['status']['discard'];
  }

  public function find($property, $value) {
    return Request::where($property, $value)->first();
  }

  public function get($user, $query) {
    if ($user->role->registrar) {
      $result = Request::OfRegistrar($user->id)->where($query)->get();
    }
    if ($user->role->dept) {
      $result = Request::OfStaff($user->id)->where($query)->get();
    }
    if ($user->role->student) {
      $result = Request::OfStudent($user->id)->where($query)->get();
    }
    return $this->markTurns($result, $user);
  }

  public function getNew($user) {
    $result = Request::where('turn', 'registrar')->get();
    return $this->markTurns($result, $user);
  }

  public function getView($user, $id) {
    $request = Request::find($id);
    $request->markMyTurn($user);
    $request->markWhoseTurn($user);
    $request->getRelations();
    return $request;
  }

  private function markTurns($requests, $user) {
    foreach ($requests as $request) {
      $request->markMyTurn($user);
      $request->markWhoseTurn();
      $request->getRelations();
    }
    return $requests;
  }

  private function filterForMyTurn($requests) {
    $arr = [];
    foreach ($requests as $request) {
      if ($request->myTurn) {
        array_push($arr, $request);
      }
    }
    return $arr;
  }

  public function add($data) {
    $data['courseID'] = $this->courseSvc->add($data['courseID']);
    $request = Request::create($data);
    $this->addRequestIDtoCourse($request->courseID, $request->id);
    return $request;
  }

  private function addRequestIDtoCourse($courseID, $requestID) {
    $this->courseSvc->edit(['requestID' => $requestID], $courseID);
  }

  public function addCourseDocument($files, $courseID) {
    $this->courseSvc->addDocument($files, $courseID);
  }

  public function removeCourseDocument($types, $id) {
    $this->courseSvc->removeDocument($types, $id);

  }

  public function edit($data, $id, $user) {
    $this->logger()->info('reqSvc:edit', [$data, $id, $user]);
    $request = Request::find($id);
    if ($this->checkUnclaimed($request) && $user->role->registrar) {
      $request->registrarStaffID = $user->id;
      $request->turn = 'registrarStaffID';
    }
    foreach ($data['values'] as $key => $value) {
      $request->$key = $value;
    }
    foreach ($data['standing'] as $key => $value) {
      $request->$key = $value;
    }
    $request->save();
    return $this->markTurns([Request::find($request->id)], $user->id)[0];
  }

  private function checkUnclaimed($request) {
    if (is_null($request->registrarStaffID)) {
      return true;
    }
    return false;
  }

  public function delete($id) {
    $this->logger()->info('reqSvc:delete', [$id]);
    $request = Request::find($id);
    $this->courseSvc->delete($request->courseID);
    $request->delete();
  }

}
