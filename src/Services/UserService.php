<?php
namespace App\Services;

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\User;

class UserService extends Service {

  public function __construct($logger) {
    parent::__construct($logger);
  }

  public function find($property, $value) {
    return User::where($property, $value)->first();
  }

  public function get($query) {
    return User::where($query)->get();
  }

  private function searchName($name) {
    $users = User::all();
    $result = [];
    foreach ($users as $user) {
      if (strpos(strtolower($user->name), strtolower($name)) !== false) {
        array_push($result, $user);
      }
    }
    return $result;
  }

  public function add($data) {
    return User::create($data);
  }

  public function edit($data, $id) {
    $user = User::find($id);
    if (is_null($user)) {
      return $this->logger()->info('user not found', [$user]);
    }
    foreach ($data as $key => $value) {
      if ($user->$key !== $value) {
        $user->$key = $value;
      }
    }
    $user->save();
    $this->logger()->info('uesrSvc:edit - user saved', [$user]);
    return $user;
  }

  public function delete($id) {
    User::destroy($id);
  }

}

 ?>
