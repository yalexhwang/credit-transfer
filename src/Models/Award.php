<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Award extends Model {

	protected $table = 'awards';

	protected $fillable = [
		'requestID',
		'gtCourseID',
		'round',
		'deptCredit',
		'registrarCredit',
		'policyRequested',
		'policyMade'
	];

	protected $casts = [
    'policyRequested' => 'boolean',
    'policyMade' => 'boolean'
	];

	protected $appends = ['models'];

	// Model relations
	public function getModelsAttribute() {
		$this->gtCourse;
	}

	public function gtCourse() {
		return $this->belongsTo('App\Models\GtCourse', 'gtCourseID');
	}

}
