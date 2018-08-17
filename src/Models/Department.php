<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Department extends Model {

	protected $table = 'departments';

	public $timestamps = false;
	
	protected $guarded = [
		'name', 
		'code'
	];

	protected $appends = ['members'];

	// Getters
  public function getMembersAttribute() {
    return DB::table('users')->where('deptID', $this->attributes['id'])->get();
  }

}