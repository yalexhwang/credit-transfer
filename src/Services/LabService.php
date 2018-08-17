<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Lab;

class LabService {

  private $logger;
  private $uploadPath;
  private $documents;

  public function __construct($logger, $uploadPath) {
    $this->logger = $logger;
    $this->uploadPath = $uploadPath;
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
  }

  private function logger() {
    return $this->logger;
  }

  public function get($query = []) {
    return Lab::where($query)->get();
  }

  public function add($data, $courseID = null) {
    $this->logger()->info('labSvc:add', [$data, $courseID]);
    $newLab = Lab::create($data);
    $newLab->courseID = $courseID;
    $newLab->save();
    $this->logger()->info('lab added', [$newLab]);
    return $newLab->id;
  }

  public function edit($data, $id) {
    $lab = Lab::find($id);
    $this->logger()->info('labSvc:edit', [$data, $lab]);
    foreach ($data as $key => $value) {
      $lab->$key = $value;
    }
    $lab->save();
    return $id;
  }

  public function delete($id) {
    $lab = Lab::find($id);
    $this->logger()->info('lab to delete', [$lab, $id]);
    if (isset($lab->fileSyllabus)) {
      $this->removeDocument('syllabus', $id);
    }
    if (isset($lab->fileDesc)) {
      $this->removeDocument('desc', $id);
    }
    $lab->delete();
  }

  private function discard($id) {
    $lab = Lab::find($id);
    $lab->statusID = 6;
    $lab->save();
  }

  public function addDocument($files, $id) {
    $this->logger()->info('add lab doc, labID...', [$files, $id]);
    foreach ($files as $typeID => $file) {
      if ($typeID == "3") {
        $column = $this->documents['syllabus']['column'];
        $naming = $this->documents['syllabus']['fileNaming'];
        $filename = $this->getFilename($id, $naming, $file);
        $this->edit([$column => $filename], $id);
        $file->moveTo($this->uploadPath . DIRECTORY_SEPARATOR . $filename);
      }
      if ($typeID == "4") {
        $column = $this->documents['description']['column'];
        $naming = $this->documents['description']['fileNaming'];
        $filename = $this->getFilename($id, $naming, $file);
        $this->edit([$column => $filename], $id);
        $file->moveTo($this->uploadPath . DIRECTORY_SEPARATOR . $filename);
      }
    }
  }

  private function getFilename($id, $type, $file) {
    return "C" . Lab::find($id)->courseID . "-" . "L" . $id . "-" . $type . "." . pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
  }

  public function removeDocument($typeIDs, $id) {
    $this->logger()->info('delete lab docs', [$typeIDs]);
    foreach ($typeIDs as $typeID) {
      if ($typeID == "3") {
        $this->logger()->info('delete lab syllabus');
        $column = 'fileSyllabus';
      }
      if ($typeID == "4") {
        $this->logger()->info('delete lab desc');
        $column = 'fileDescription';
      }
      $filename = Lab::find($id)->$column;
      $this->edit([$column => null], $id);
      unlink($this->uploadPath . DIRECTORY_SEPARATOR . $filename);
    }
  }

}
