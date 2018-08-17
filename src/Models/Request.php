<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model {

	protected $table = 'requests';

	protected $fillable = [
		'statusID', 
		'stepID', 
		'turn', 
		'courseID', 
		'studentID', 
		'registrarStaffID', 
		'deptID',
		'deptStaffID', 
		'admissionRequired', 
		'equivSoughtSubjectID', 
		'equivSoughtNumber', 
		'bypassed'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];

	protected $casts = [
    'bypassed'  => 'boolean',
    'admissionRequired' => 'boolean'
	];

	protected $with = [
		'course',
		'student'
	];

	protected $appends = [
		'status',
		'step',
		'logs',
		'registrar'
	];

	public function scopeOfRegistrar($query, $id) {
		return $query->where('registrarStaffID', $id);
	}

	public function scopeOfStaff($query, $id) {
		return $query->where('deptStaffID', $id);
	}

	public function scopeOfStudent($query, $id) {
		return $query->where('studentID', $id);
	}

	public function course() {
		return $this->hasOne('App\Models\Course', 'requestID');
	}

	public function student() {
		return $this->belongsTo('App\Models\User', 'studentID');
	}

	public function getStatusAttribute() {
		return DB::table('_status')->where('id', $this->attributes['statusID'])->first();
	}

	public function getStepAttribute() {
		return DB::table('_steps')->where('id', $this->attributes['stepID'])->first();
	}

	public function getLogsAttribute() {
		return DB::table('logs')->where('requestID', $this->attributes['id'])->get();
	}

	public function getRegistrarAttribute() {
		if (isset($this->attributes['registrarStaffID'])) {
			return DB::table('users')->where('id', $this->attributes['registrarStaffID'])->first();
		}
		return null;
	}

	public function evaluation() {
		return $this->hasOne('App\Models\Evaluation', 'requestID');
	}

	private function getDepartment() {
		return DB::table('departments')->where('id', $this->attributes['deptID'])->first();
	}

	private function getStaff() {
		return DB::table('users')->where('id', $this->attributes['deptStaffID'])->first();
	}

	private function getEquivSoughtSubject() {
		return DB::table('subjects')->where('id', $this->attributes['equivSoughtSubjectID'])->first();
	}

	public function getRelations() {
		$this->equivSoughtSubject = $this->getEquivSoughtSubject();
		if ($this->attributes['stepID'] > 1) {
			$this->department = $this->getDepartment();
			$this->staff = $this->getStaff();
		}
		if ($this->attributes['stepID'] > 2) {
			$this->evaluation;
		}
	}

	public function markMyTurn($user) {
		if ($this->attributes['turn'] === 'registrar' || $this->attributes['turn'] === 'registrarStaffID') {
			if ($user->role->registrar) {
				return $this->myTurn = true;
			}
		}
		$turn = $this->attributes['turn'];
		if ($this->attributes[$turn] === $user->id) {
			return $this->myTurn = true;
		}
		$this->myTurn = false;
	}

	public function markWhoseTurn() {
		if ($this->attributes['turn'] === 'registrar' || $this->attributes['turn'] === 'registrarStaffID') {
			return $this->whoseTurn = 'Registrar';
		}
		if ($this->attributes['turn'] === 'deptStaffID') {
			return $this->whoseTurn = 'Department';
		}
		if ($this->attributes['turn'] === 'studentID') {
			return $this->whoseTurn = 'Student';
		}
		$this->whoseTurn = '-';
	}


}
