<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Action extends Model {

	protected $table = '_actions';
  
	protected $guarded = [
    'hasSub', 
    'subTo',
    'stepID',
    'role',
    'key',
    'nextTurn',
    'nextStepID', 
    'nextStatusID',
    'logTemplateID',
    'UIdisplay'
  ];

	protected $casts = [
 		'hasSub' => 'boolean'
	];

  protected $appends = ['subs'];

  public function scopeMain($query) {
    return $query->whereNotNull('hasSub');
  }
  
	public function scopeOfStep($query, $stepID) {
    return $query->where('stepID', $stepID)->orWhere('stepID', 0);
  }

  public function scopeRegistrar($query) {
    return $query->where('role', 'registrar');
  }

  public function scopeDept($query) {
    return $query->where('role', 'department');
  }

  public function scopeStudent($query) {
    return $query->where('role', 'student');
  }

  // public function logTemplate() {
  //   return $this->hasOne('App\Models\LogTemplate', 'actionID');
  // }

  // Getters
  public function getSubsAttribute() {
    return DB::table('_actions')->where('subTo', $this->attributes['id'])->get();
  }

}
