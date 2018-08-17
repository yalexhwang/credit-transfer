<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lab extends Model {

	protected $table = 'course_labs';
	
	protected $fillable = [
		'courseID',
		'prefix', 
		'number', 
		'title',
		'creditHours', 
		'fileSyllabus', 
		'fileDescription'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	
	// Setters
	public function setPrefixAttribute($value) {
		$this->attributes['prefix'] = strtoupper($value);
	}

	public function setTitleAttribute($value) {
		$this->attributes['title'] = ucwords(strtolower($value));
	}


}
