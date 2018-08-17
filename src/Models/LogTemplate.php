<?php
namespace App\Models;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class LogTemplate extends Model {

	protected $table = '_log_templates';

	protected $guarded = [
		'description',
		'hasNote',
		'itemTemplate',
		'isItemMulti'
	];

	protected $casts = [
		'isItemMulti' => 'boolean'
	];

}
