<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use App\Models\Course;
use App\Models\Lab;
use App\Models\Request;
use App\Services\LabService;

class CourseService extends Service {

  private $uploadPath;
  private $documents;

  public function __construct($logger, $settings) {
    parent::__construct($logger);
    $this->uploadPath = $settings['upload_path'];
    $this->documents = [
      'syllabus' => [
        'column' => 'fileSyllabus',
        'fileNaming' => 'syllabus'
      ],
      'description' => [
        'column' => 'fileDescription',
        'fileNaming' => 'description'
      ],
    ];

    $this->labSvc = new LabService($logger, $this->uploadPath);
  }

  public function get($query) {
    return Course::where($query)->get();
  }

  public function add($data) {
    $this->logger()->info('courseSvc: add', [$data]);
    if (isset($data['labID'])) {
      $lab = Lab::create($data);
      $data['labID'] = $lab->id;
    }
    $this->logger()->info('lab processed/labID added', [$data]);
    $newCourse = Course::create($data);
    if (isset($data['labID'])) {
      $this->logger()->info('has lab, updated lab with courseID');
      $lab->courseID = $newCourse->id;
      $lab->save();
    }
    return $newCourse->id;
  }

  private function getLabID($labData) {
    $this->logger()->info('courseSvc: getLabID', [$labData]);
    if (is_null($labData)) {
      $this->logger()->info('...labData is null');
      return null;
    }
    $this->logger()->info('labData exists, add lab...');
    return Lab::create($labData);
  }


  public function edit($data, $id) {
    $this->logger()->info('courseSvc: edit', [$data]);
    $course = Course::find($id);
    foreach ($data as $key => $value) {
      if ($value !== $course->$key) {
        $this->logger()->info('courseSvc: key to edit', [$key]);
        if ($key == 'labID') {
          $value = $this->updateLab($course, $value);
        }
        $course->$key = $value;
      }
    }
    $course->save();
    return $course;
  }

  private function updateLab($course, $labData) {
    if ($course->labID !== 0 && $labData !== 0) {
      $this->logger()->info('compare details');
      return $this->labSvc->edit($labData, $course->labID);
    }
    if ($course->labID == 0 && $labData !== 0) {
      $this->logger()->info('lab added');
      return $this->labSvc->add($labData, $course->id);
    }
    if ($course->labID !== 0 && $labData == 0) {
      $this->logger()->info('lab removed', [$course->labID]);
      return $this->labSvc->delete($course->labID);
    }
    if ($course->labID == 0 && $labID == 0) {
      $this->logger()->info('no lab before and after');
      return $labID;
    }
  }

  public function delete($id) {
    $this->logger()->info('deleting a course', [$id]);
    $course = Course::find($id);
    if (isset($course->labID)) {
      $this->labSvc->delete($course->labID);
    }
    if (isset($course->fileSyllabus)) {
      $this->removeDocument('1', $id);
    }
    if (isset($course->fileDesc)) {
      $this->removeDocument('2', $id);
    }
    $course->delete();
  }

  public function addDocument($files, $id) {
    if (isset($files['3']) || isset($files['4'])) {
      $this->labSvc->addDocument($files, Course::find($id)->labID);
    }
    foreach ($files as $typeID => $file) {
      if ($typeID == "1") {
        $column = $this->documents['syllabus']['column'];
        $filename = $this->getFilename($id, $this->documents['syllabus']['fileNaming'], $file);
        $this->edit([$column => $filename], $id);
        $file->moveTo($this->uploadPath . DIRECTORY_SEPARATOR . $filename);
      }
      if ($typeID == "2") {
        $column = $this->documents['description']['column'];
        $filename = $this->getFilename($id, $this->documents['description']['fileNaming'], $file);
        $this->edit([$column => $filename], $id);
        $file->moveTo($this->uploadPath . DIRECTORY_SEPARATOR . $filename);
      }
    }
  }

  private function getFilename($id, $type, $file) {
    return "C" . $id . "-" . $type . "." . pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
  }

  public function removeDocument($types, $id) {
    $typeIDs = explode(",", $types);
    foreach ($typeIDs as $typeID) {
      if ($typeID == "3" || $typeID == "4") {
        return $this->labSvc->removeDocument($typeIDs, Course::find($id)->labID);
      }
      if ($typeID == "1") {
        $column = 'fileSyllabus';
      }
      if ($typeID == "2") {
        $column = 'fileDescription';
      }
      $filename = Course::find($id)->$column;
      $this->logger()->info('filename to delete', [$filename]);
      $this->edit([$column => null], $id);
      unlink($this->uploadPath . DIRECTORY_SEPARATOR . $filename);
    }
  }


}
