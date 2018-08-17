<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\GtCourse;

class GtCourseService extends Service {

  private $appends;

  public function __construct($logger) {
    parent::__construct($logger);
    $this->appends = ['dept'];
  }

  public function get($query) {
    return GtCourse::where($query)->get();
  }

  public function add($course) {
    if ($this->findDuplicate($course)) {
      $this->logger()->info('duplicate GtCourse found', [$course]);
      return 'duplicate found';
    }
    $newGtCourse = GtCourse::create($course);
    $newGtCourse->title = ucwords(strtolower($course['title']));
    $newGtCourse->deptID = DB::table('_departments')->where('code', $course['prefix'])->value('id');
    $newGtCourse->save();
    $this->logger()->info('new Gt Course added/saved', [$course]);
    $this->logger()->info('gt course created', [$newGtCourse]);
    return $newGtCourse;
  }

  private function findDuplicate($course) {
    return GtCourse::where(['prefix' => $course['prefix'], 'number' => $course['number']])->first();
  }

  public function edit($courseData, $query) {
    $course = GtCourse::find($query['id']);
    foreach ($courseData as $key => $value) {
      $this->logger()->info('...', [$key, $course->$key, $value]);
      if ($key !== 'numberOnly') {
        if ($value !== $course->$key) {
          $this->logger()->info('different!', [$key, $value]);
          $course->$key = $value;
        }
      }
    }
    $course->save();
    $this->logger()->info('course updated and saved', $course);
    return $this->getBy(['id' => $course->id]);
  }

  public function delete($id) {
    $course = GtCourse::find($id);
    $course->delete();
  }


}
