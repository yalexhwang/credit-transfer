<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class GtCourse extends Model {

	protected $table = 'gt_courses';

	protected $fillabe = [
		'isLab', 
		'prefix', 
		'level', 
		'isElective', 
		'number', 
		'minCredit', 
		'maxCredit'
	];

	protected $casts = [
    'isLab'  => 'boolean',
    'isElective' => 'boolean'
	];

}
