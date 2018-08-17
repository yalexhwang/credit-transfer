<?php
namespace App\Models;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class Log extends Model {

	protected $table = 'logs';

	protected $fillable = [
		'requestID', 
		'logTemplateID', 
		'stepID',
		'description', 
		'itemString', 
		'note'
	];
	
}
