<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class School extends Model {

	protected $table = 'schools';
	
	public $timestamps = false;
	
	protected $fillable = [
		'international', 
		'name', 
		'country', 
		'city', 
		'state', 
		'zipcode',
		'website'
	];

	// Setters
	public function setNameAttribute($value) {
		$this->attributes['name'] = ucwords(strtolower($value));
	}

	public function setCityAttribute($value) {
		$this->attributes['city'] = ucwords(strtolower($value));
	}

	public function setStateAttribute($value) {
		if (empty($value)) {
			return $this->attributes['state'] = null;
		}
		$this->attributes['state'] = ucwords($value);
	}

	public function setCountryAttribute($value) {
		$this->attributes['country'] = ucwords($value);
	}

}
