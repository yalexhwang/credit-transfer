<?php
namespace App\Models;

use App\Models\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model {

	protected $table = 'users';
	
	protected $fillable = [
		'username', 
		'gtID',
		'name',
		'email',
		'roleID', 
		'deptID'
	];

	use SoftDeletes;
	protected $dates = [
		'created_at', 'updated_at', 'deleted_at'
	];

	protected $appends = ['role'];

	public function department() {
		return $this->belongsTo('App\Models\Department', 'deptID');
	}

	// Getters
	public function getRoleAttribute() {
		return DB::table('roles')->where('id', $this->attributes['roleID'])->first();
	}

	public function getRelations() {
		$this->department = $this->getDepartment();
	}

	private function getDepartment() {
		return DB::table('departments')->where('id', $this->attributes['deptID'])->first();
	}

}
