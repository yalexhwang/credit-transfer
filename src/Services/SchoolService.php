<?php
namespace App\Services;

use App\Models\School;

class SchoolService extends Service {

  public function __construct($logger) {
    parent::__construct($logger);
  }

  public function get($query) {
    return School::where($query)->get();
  }

  public function getStateList() {
    $schools = School::where('country', 'USA')->get();
    return $schools->groupBy('state')->keys();
  }

  public function getCountryList() {
    $schools = School::where('country', '<>', 'USA')->get()->sortBy('country');
    // $schools = School::where('country', '<>', 'USA')->get();
    return $schools->groupBy('country')->keys();
  }

  public function add($school) {
    $newSchool = School::create($school);
    if ($newSchool->country !== 'USA') {
      $newSchool->international = 1;
    }
    $newSchool->save();
    $this->logger()->info('new school saved', [$newSchool]);
    return $newSchool;
  }

  public function edit($data, $id) {
    $school = School::find($id);
     $this->logger()->info('school to edit', [$school]);
     foreach ($data as $key => $value) {
      if ($value !== $school->$key) {
        $school->$key = $value;
      }
      $school->save();
      $this->logger()->info('school edited/saved', [$school]);
     }
     return $school;
  }

  public function delete($id) {
    $this->logger()->info('school to delete', [$id]);
    $school = School::find($id);
    $school->delete();
  }

}
