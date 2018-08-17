<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Award;

class Evaluation extends Model {

	protected $table = 'evaluations';

	protected $fillable = [
		'requestID',
		'proposedAwardIDs',
		'finalAwardIDs'
	];

	protected $appends = ['proposed', 'final'];

	public function getProposedAttribute() {
		if ($this->attributes['proposedAwardIDs'] === "" || is_null($this->attributes['proposedAwardIDs'])) {
			return null;
		}
		$ids = explode(",", $this->attributes['proposedAwardIDs']);
		$arr = [];
		foreach ($ids as $id) {
			array_push($arr, Award::find($id));
		}
		return $arr;
	}

	public function getFinalAttribute() {
		if ($this->attributes['finalAwardIDs'] === "" || is_null($this->attributes['finalAwardIDs'])) {
			return null;
		}
		$ids = explode(",", $this->attributes['finalAwardIDs']);
		$arr = [];
		foreach ($ids as $id) {
			array_push($arr, Award::find($id));
		}
		return $arr;
	}

}
