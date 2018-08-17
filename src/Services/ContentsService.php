<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Department;
use App\Models\School;
class ContentsService extends Service {

  private $table;

  public function __construct($logger) {
    parent::__construct($logger);
  }

  public function getUIcontents($component) {
    $contents = DB::table('UI_contents')->where('component', $component)->get();
    return $this->processUIcountents($contents);
  }

  private function processUIcountents($contents) {
    $arr = [];
    foreach ($contents as $row) {
      $values = explode(",", $row->valueString);
      if (isset($row->propertyString)) {
        $props = explode(",", $row->propertyString);
        array_push($arr, $this->convertToObject($props, $values));
      } else {
        $arr[$row->name] = explode(",", $row->valueString);
      }
    }
    $this->logger()->info('processUIcountents', [$arr]);
    return $arr;
  }

  private function convertToObject($props, $propValues) {
    $arr = [];
    foreach ($props as $key => $prop) {
      $arr[$prop] = $propValues[$key];
    }
    return $arr;
  }

  private function convertToArray($valueString) {

  }

  public function getAction() {
    return DB::table('_actions')->get();
  }

  public function getCountry() {
    $arr = School::where('international', 1)->get()->keyBy('country');
    return $arr->keys()->all();
  }

  public function getDepartment($query) {
    $this->logger()->info('contSvc:getDepartment', [$query]);
    if (isset($query['value'])) {
      return Department::find($query['id'])->$query['value'];
    }
    return Department::where($query)->get();
  }

  public function getRequestStatus() {
    return DB::table('status')->where('type', 'request')->get(); 
  }

  public function getStaff($query) {
    if (empty($query)) {
      // return all staff?
    }
    if ($query['deptID']) {
      $this->logger()->info('getting staff', [$query]);
      return DB::table('users')->where('deptID', $query['deptID'])->get();
    }
  }

  public function getState() {
    return DB::table('UI_contents')->where('name', 'state')->get();
  }

  public function getStatus() {
    return DB::table('_status')->all();
  }

  public function getStep() {
    return DB::table('_steps')->get();
  }

  public function getSubject() {
    return DB::table('subjects')->get();
  }

  public function getSchool($query) {
    if (empty($query)) {
      return School::all();
    }
    if ($query['international']) {
      $this->logger()->info('internaitonal schools');
      return School::where('international', 1)->get();
    } 
    $this->logger()->info('USA schools with state');
    return School::where('state', $query['state'])->get();
  }

}
