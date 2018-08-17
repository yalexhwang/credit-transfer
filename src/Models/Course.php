<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model {

	protected $table = 'courses';

	protected $fillable = [
		'labID', 
		'requestID', 
		'prefix', 
		'number', 
		'title', 
		'schoolID',
		'year',
		'season',
		'term',
		'creditHours', 
		'fileSyllabus', 
		'fileDescription'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	
	protected $appends = [
		'lab',
		'school'
	];

	public function lab() {
		return $this->hasOne('App\Models\Lab', 'courseID');
	}

	public function school() {
		return $this->belongsTo('App\Models\School', 'schoolID');
	}

	public function getLabAttribute() {
		return DB::table('course_labs')->where('id', $this->attributes['labID'])->first();
	}

	public function getSchoolAttribute() {
		return DB::table('schools')->where('id', $this->attributes['schoolID'])->first();
	}

	public function setPrefixAttribute($value) {
		$this->attributes['prefix'] = strtoupper($value);
	}

	public function setTitleAttribute($value) {
		$this->attributes['title'] = ucwords(strtolower($value));
	}

	public function setSeasonAttribute($value) {
		$this->attributes['season'] = ucwords(strtolower($value));
	}

	public function setTermAttribute($value) {
		$this->attributes['term'] = ucwords(strtolower($value));
	}
	
}
